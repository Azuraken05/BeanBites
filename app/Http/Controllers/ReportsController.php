<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // 1. Summary Cards Panel Counters
        $todaySales = Order::whereDate('created_at', Carbon::today())->sum('total_amount');
        $todayOrdersCount = Order::whereDate('created_at', Carbon::today())->count();

        $weekSales = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
        $weekOrdersCount = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $totalSalesOverall = Order::sum('total_amount');

        // 2. Sales Analytics Graph Bars Streams Mapping
        $daysOfWeek = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
        $weeklyChartData = [];
        foreach (range(1, 7) as $dayNumber) {
            $mysqlDayIndex = ($dayNumber % 7) + 1;
            $weeklyChartData[] = (float) Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereRaw("DAYOFWEEK(created_at) = {$mysqlDayIndex}")
                ->sum('total_amount');
        }

        $monthlyChartLabels = ['W1', 'W2', 'W3', 'W4', 'W5'];
        $monthlyChartData = [0, 0, 0, 0, 0];
        $monthOrders = Order::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        foreach ($monthOrders as $order) {
            $orderDate = Carbon::parse($order->created_at);
            $weekNumber = ceil($orderDate->day / 7) - 1; 
            if ($weekNumber > 4) $weekNumber = 4;
            $monthlyChartData[$weekNumber] += (float) $order->total_amount;
        }

        // 3. Transactions Log Data Feeds Groups
        $dailyTransactions = Order::select(DB::raw('DATE(created_at) as log_date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(id) as orders_count'))
            ->groupBy('log_date')->orderBy('log_date', 'desc')->take(15)->get()
            ->map(function($item) {
                return [
                    'title' => Carbon::parse($item->log_date)->format('F d, Y'),
                    'subtitle' => $item->orders_count . ' Orders',
                    'amount' => number_format($item->total_sales, 2)
                ];
            });

        $weeklyTransactions = Order::select(DB::raw('YEAR(created_at) as year'), DB::raw('WEEK(created_at, 1) as week_num'), DB::raw('MIN(created_at) as week_start'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(id) as orders_count'))
            ->groupBy('year', 'week_num')->orderBy('year', 'desc')->orderBy('week_num', 'desc')->take(10)->get()
            ->map(function($item) {
                $start = Carbon::parse($item->week_start)->startOfWeek()->format('M d');
                $end = Carbon::parse($item->week_start)->endOfWeek()->format('M d, Y');
                return [
                    'title' => "Week ({$start} - {$end})",
                    'subtitle' => $item->orders_count . ' Orders total',
                    'amount' => number_format($item->total_sales, 2)
                ];
            });

        // 4. Low Stock Monitor Alarm Limits Data Arrays
        $lowStockProducts = Product::whereNotNull('stock')->where('stock', '<=', 5)->orderBy('stock', 'asc')->get();

        // 5. NEW: Pull down soft-deleted records paired alongside authorized profile details
        $deletedProducts = Product::onlyTrashed()->with('deletedBy')->orderBy('deleted_at', 'desc')->get();

        return view('reports', compact(
            'todaySales', 'todayOrdersCount', 'weekSales', 'weekOrdersCount', 'totalSalesOverall',
            'daysOfWeek', 'weeklyChartData', 'monthlyChartLabels', 'monthlyChartData',
            'dailyTransactions', 'weeklyTransactions', 'lowStockProducts', 'deletedProducts'
        ));
    }
}