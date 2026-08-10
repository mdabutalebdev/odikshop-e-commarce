<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $paidStatuses = ['processing', 'shipped', 'delivered'];

        $stats = [
            'revenue' => (float) Order::whereIn('status', $paidStatuses)->sum('total'),
            'orders' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'products' => Product::count(),
            'customers' => User::where('is_admin', false)->count(),
            'reviews' => ProductReview::where('status', 'pending')->count(),
        ];

        // Last 7 days revenue for the chart
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $salesChart[] = [
                'label' => $day->format('D'),
                'value' => (float) Order::whereDate('created_at', $day->toDateString())->sum('total'),
            ];
        }

        // Order status breakdown
        $statusBreakdown = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status')->all();

        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')->take(5)->get();

        $recentOrders = Order::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'salesChart', 'statusBreakdown', 'topProducts', 'recentOrders'));
    }
}
