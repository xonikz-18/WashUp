<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:Pending,Ready for Pickup,Claimed'],
        ]);

        $orders = Order::with(['service', 'orderAddons.addon'])
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'ilike', "%{$search}%")
                        ->orWhere('customer_name', 'ilike', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('date_created')
            ->orderByDesc('id')
            ->get();

        return response()->json($orders->map(fn (Order $order) => $this->formatOrder($order)));
    }

    public function show(Order $order)
    {
        $order->load(['service', 'orderAddons.addon']);

        return response()->json($this->formatOrder($order));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'weight' => ['required', 'numeric', 'gt:0'],
            'estimated_pickup' => ['required', 'date'],
            'addons' => ['nullable', 'array'],
            'addons.*.addon_id' => ['required', 'integer', 'exists:addons,id'],
            'addons.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $service = Service::findOrFail($validated['service_id']);
            $selectedAddons = $this->getSelectedAddons($validated['addons'] ?? []);

            $serviceCost = $service->price_per_kg * $validated['weight'];
            $addonsCost = 0;

            foreach ($selectedAddons as $selectedAddon) {
                $addonsCost += $selectedAddon['addon']->addon_price * $selectedAddon['quantity'];
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'phone_number' => $validated['phone_number'],
                'service_id' => $service->id,
                'weight' => $validated['weight'],
                'total_price' => round($serviceCost + $addonsCost, 2),
                'status' => 'Pending',
                'estimated_pickup' => $validated['estimated_pickup'],
                'date_created' => now(),
            ]);

            foreach ($selectedAddons as $selectedAddon) {
                $order->orderAddons()->create([
                    'addon_id' => $selectedAddon['addon']->id,
                    'quantity' => $selectedAddon['quantity'],
                    'addon_price' => $selectedAddon['addon']->addon_price,
                ]);
            }

            return $order;
        });

        $order->load(['service', 'orderAddons.addon']);

        return response()->json($this->formatOrder($order), 201);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Ready for Pickup,Claimed'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        $order->load(['service', 'orderAddons.addon']);

        return response()->json($this->formatOrder($order));
    }

    private function getSelectedAddons(array $requestedAddons): array
    {
        $addonIds = collect($requestedAddons)->pluck('addon_id')->unique();
        $addons = Addon::whereIn('id', $addonIds)->get()->keyBy('id');

        return collect($requestedAddons)->map(function ($requestedAddon) use ($addons) {
            return [
                'addon' => $addons[$requestedAddon['addon_id']],
                'quantity' => $requestedAddon['quantity'],
            ];
        })->all();
    }

    private function generateOrderNumber(): string
    {
        $lastOrder = Order::lockForUpdate()->orderByDesc('id')->first();
        $nextNumber = $lastOrder
            ? (int) substr($lastOrder->order_number, 3) + 1
            : 1;

        return 'WU-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'phone_number' => $order->phone_number,
            'service' => [
                'id' => $order->service->id,
                'service_name' => $order->service->service_name,
                'price_per_kg' => $order->service->price_per_kg,
            ],
            'weight' => $order->weight,
            'addons' => $order->orderAddons->map(function ($orderAddon) {
                return [
                    'id' => $orderAddon->addon->id,
                    'addon_name' => $orderAddon->addon->addon_name,
                    'quantity' => $orderAddon->quantity,
                    'addon_price' => $orderAddon->addon_price,
                ];
            })->values(),
            'total_price' => $order->total_price,
            'status' => $order->status,
            'estimated_pickup' => $order->estimated_pickup,
            'date_created' => $order->date_created,
        ];
    }
}
