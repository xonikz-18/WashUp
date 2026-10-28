<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse(
            $validated['start_date'] ?? now()->startOfMonth()->toDateString()
        )->startOfDay();

        $endDate = Carbon::parse(
            $validated['end_date'] ?? now()->toDateString()
        )->endOfDay();

        $baseQuery = Feedback::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalFeedback = (clone $baseQuery)->count();

        $averageRating = (clone $baseQuery)->avg('rating');

        $complaints = (clone $baseQuery)
            ->where('type', 'Complaint')
            ->count();

        $feedback = (clone $baseQuery)
            ->with([
                'order:id,order_number,customer_name'
            ])
            ->latest('created_at')
            ->get();

        $rows = $feedback->map(function (Feedback $item) {
            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'order_number' => $item->order?->order_number,
                'customer_name' => $item->order?->customer_name,
                'rating' => (int) $item->rating,
                'type' => $item->type,
                'comment' => $item->comment,
                'created_at' => $item->created_at,
            ];
        })->values();

        return response()->json([
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_feedback' => $totalFeedback,
                'average_rating' => $averageRating !== null
                    ? round((float) $averageRating, 1)
                    : 0,
                'complaints' => $complaints,
            ],
            'feedback' => $rows,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'type' => [
                'required',
                'string',
                'in:Feedback,Complaint,Suggestion'
            ],
            'comment' => ['required', 'string', 'max:3000'],
        ]);

        $orderNumber = strtoupper(trim($validated['order_number']));

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'message' => 'We could not find an order with that Order Number.',
            ], 404);
        }

        // Quick check for an existing feedback submission.
        if (Feedback::where('order_id', $order->id)->exists()) {
            return response()->json([
                'message' => 'This order already has feedback submitted.',
            ], 409);
        }

        /*
         * Lock the order while creating feedback.
         * This prevents two simultaneous submissions
         * from bypassing the duplicate check.
         */
        $feedback = DB::transaction(function () use ($order, $validated) {

            $lockedOrder = Order::where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (Feedback::where('order_id', $lockedOrder->id)->exists()) {
                return null;
            }

            return Feedback::create([
                'order_id' => $lockedOrder->id,
                'rating' => $validated['rating'],
                'type' => $validated['type'],
                'comment' => trim($validated['comment']),
            ]);
        });

        if (!$feedback) {
            return response()->json([
                'message' => 'This order already has feedback submitted.',
            ], 409);
        }

        $feedback->load([
            'order:id,order_number,customer_name'
        ]);

        return response()->json([
            'message' => 'Your feedback has been submitted successfully.',
            'feedback' => [
                'id' => $feedback->id,
                'order_number' => $feedback->order?->order_number,
                'customer_name' => $feedback->order?->customer_name,
                'rating' => (int) $feedback->rating,
                'type' => $feedback->type,
                'comment' => $feedback->comment,
                'created_at' => $feedback->created_at,
            ],
        ], 201);
    }
}