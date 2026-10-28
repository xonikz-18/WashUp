<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
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
            $validated['end_date'] ?? now()->endOfMonth()->toDateString()
        )->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Base Orders Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = Order::query()
            ->whereBetween('date_created', [
                $startDate,
                $endDate,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalOrders = (clone $baseQuery)->count();

        $totalRevenue = (clone $baseQuery)->sum('total_price');

        $pending = (clone $baseQuery)
            ->where('status', 'Pending')
            ->count();

        $readyForPickup = (clone $baseQuery)
            ->where('status', 'Ready for Pickup')
            ->count();

        $claimed = (clone $baseQuery)
            ->where('status', 'Claimed')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Daily Orders + Revenue
        |--------------------------------------------------------------------------
        */

        $dailyData = (clone $baseQuery)
            ->selectRaw("
                DATE(date_created) AS report_date,
                COUNT(*) AS order_count,
                COALESCE(SUM(total_price), 0) AS revenue
            ")
            ->groupByRaw('DATE(date_created)')
            ->orderByRaw('DATE(date_created)')
            ->get()
            ->keyBy(function ($row) {
                return Carbon::parse($row->report_date)->toDateString();
            });

        $ordersOverTime = [];
        $revenueOverTime = [];

        $currentDate = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($currentDate->lte($lastDate)) {
            $dateKey = $currentDate->toDateString();

            $dayData = $dailyData->get($dateKey);

            $orderCount = $dayData
                ? (int) $dayData->order_count
                : 0;

            $revenue = $dayData
                ? (float) $dayData->revenue
                : 0;

            $ordersOverTime[] = [
                'date' => $dateKey,
                'label' => $currentDate->format('M j'),
                'orders' => $orderCount,
            ];

            $revenueOverTime[] = [
                'date' => $dateKey,
                'label' => $currentDate->format('M j'),
                'revenue' => $revenue,
            ];

            $currentDate->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],

            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => (float) $totalRevenue,
                'pending' => $pending,
                'ready_for_pickup' => $readyForPickup,
                'claimed' => $claimed,
            ],

            'orders_over_time' => $ordersOverTime,

            'revenue_over_time' => $revenueOverTime,
        ]);
    }
}