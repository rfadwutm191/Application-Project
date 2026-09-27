<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pesanan aktif
        $activeOrdersCount = Order::whereNotIn('status', ['selesai', 'completed', 'batal', 'rejected'])->count();

        // Pesanan selesai
        $completedTodayCount = Order::whereIn('status', ['selesai', 'completed'])
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // Total Pendapatan
        $totalRevenue = Order::whereIn('status', ['selesai', 'completed'])->sum('total_biaya');

        // Pesanan Baru Masuk Hari Ini
        $newOrdersToday = Order::whereDate('created_at', Carbon::today())->count();

        // Transaksi Laundry Terbaru 
        $recentOrders = Order::with(['customer', 'detailOrders', 'logNotifikasiWa'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'activeOrdersCount',
            'completedTodayCount',
            'totalRevenue',
            'newOrdersToday',
            'recentOrders'
        ));
    }
}
