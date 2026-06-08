<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core Summary Metrics Card Stats
        $ordersProcessedCount = Order::count();
        $totalProfitOverall = Order::sum('total_amount');
        
        // 2. Available Active Products Listing
        $availableProducts = Product::orderBy('name', 'asc')->get();
        $totalCustomers = Order::distinct('user_id')->count(); // Placeholder mapping or customer unique track counter

        // 3. Sales Overview: WEEKLY breakdowns (Mon - Sun)
        $daysOfWeekLabels = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
        $weeklySalesOverview = [];
        foreach (range(1, 7) as $dayNumber) {
            $mysqlDayIndex = ($dayNumber % 7) + 1;
            $weeklySalesOverview[] = (float) Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereRaw("DAYOFWEEK(created_at) = {$mysqlDayIndex}")
                ->sum('total_amount');
        }

        // 4. Sales Overview: MONTHLY breakdowns (W1 - W5)
        $monthlySalesOverview = [0, 0, 0, 0, 0];
        $monthOrders = Order::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        foreach ($monthOrders as $order) {
            $orderDate = Carbon::parse($order->created_at);
            $weekIndex = ceil($orderDate->day / 7) - 1;
            if ($weekIndex > 4) $weekIndex = 4;
            $monthlySalesOverview[$weekIndex] += (float) $order->total_amount;
        }

        // 5. Category Transactions Share Percentages (Drinks, Food, Desserts)
        $categorySalesData = OrderItem::select('product_name', 'quantity', 'price')
            ->get()
            ->groupBy(function($item) {
                // Find matching product context to get its real category grouping securely
                $productRecord = Product::withTrashed()->where('name', $item->product_name)->first();
                return $productRecord ? $productRecord->category : 'Drinks';
            });

        $categoryTotals = ['Drinks' => 0, 'Food' => 0, 'Desserts' => 0];
        $grandCategorySum = 0;

        foreach ($categorySalesData as $categoryName => $itemsCollection) {
            $sum = 0;
            foreach ($itemsCollection as $item) {
                $sum += ($item->quantity * $item->price);
            }
            if (array_key_exists($categoryName, $categoryTotals)) {
                $categoryTotals[$categoryName] = $sum;
                $grandCategorySum += $sum;
            }
        }

        // Compute real distribution percentages map arrays for circle graphs gauges
        $categoryPercentages = [
            'Drinks' => $grandCategorySum > 0 ? round(($categoryTotals['Drinks'] / $grandCategorySum) * 100) : 0,
            'Food' => $grandCategorySum > 0 ? round(($categoryTotals['Food'] / $grandCategorySum) * 100) : 0,
            'Desserts' => $grandCategorySum > 0 ? round(($categoryTotals['Desserts'] / $grandCategorySum) * 100) : 0,
        ];

        return view('dashboard', compact(
            'ordersProcessedCount', 'totalProfitOverall', 'availableProducts', 'totalCustomers',
            'daysOfWeekLabels', 'weeklySalesOverview', 'monthlySalesOverview', 'categoryPercentages'
        ));
    }
}