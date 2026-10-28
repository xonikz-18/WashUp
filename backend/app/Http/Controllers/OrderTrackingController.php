<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;

class OrderTrackingController extends Controller
{
    public function show(string $orderNumber)
    {
        $orderNumber = strtoupper(trim($orderNumber));

        $order = Order::with('service')
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found.',
            ], 404);
        }

        $hasFeedback = Feedback::where(
            'order_id',
            $order->id
        )->exists();

        return response()->json([
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'service' => [
                'id' => $order->service?->id,
                'service_name' => $order->service?->service_name,
                'price_per_kg' => $order->service_price_per_kg
                    ?? $order->service?->price_per_kg,
            ],
            'weight' => $order->weight,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'estimated_pickup' => $order->estimated_pickup,
            'date_created' => $order->date_created,
            'has_feedback' => $hasFeedback,
        ]);
    }
}
