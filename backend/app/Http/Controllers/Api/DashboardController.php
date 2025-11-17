<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::where('role', 'client')->count();
        $totalProducts = Product::count();

        $monthlyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total');

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'total_products' => $totalProducts,
            'monthly_revenue' => $monthlyRevenue,
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
        ]);
    }
}
