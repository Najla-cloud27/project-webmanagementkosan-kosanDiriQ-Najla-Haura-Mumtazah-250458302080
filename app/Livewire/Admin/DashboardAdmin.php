<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Rooms;
use App\Models\Bookings;
use App\Models\Bills;
use App\Models\Complaints;
use App\Models\PaymentProofs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdmin extends Component
{
    public $stats = [];
    public $revenueChart = [];
    public $roomStatusChart = [];
    public $bookingTrend = [];
    public $latestBookings = [];
    public $latestComplaints = [];
    public $pendingPaymentProofs = [];

    public function mount()
    {
        // ----- STATISTICS -----
        $this->stats = [
            'total_rooms' => Rooms::count(),
            'available_rooms' => Rooms::where('status', 'tersedia')->count(),
            'total_tenants' => User::where('role', 'penyewa')->count(),
            'active_bookings' => Bookings::whereIn('status', ['pending', 'menunggu_verifikasi', 'dikonfirmasi'])->count(),
            'unpaid_bills' => Bills::whereIn('status', ['belum_dibayar', 'overdue'])->count(),
            'monthly_income' => Bills::where('status', 'dibayar')->whereMonth('payment_date', Carbon::now()->month)->sum('total_amount'),
            'pending_payments' => PaymentProofs::where('status', 'pending')->count(),
            'active_complaints' => Complaints::where('status', ['dikirim', 'diproses'])->count(),
        ];

        // ----- 6 MONTH REVENUE CHART -----
        // grafik pendapatan 6 bulan
        $this->revenueChart = Bills::where('status', 'dibayar')
           ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(total_amount) as total")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->toArray();

        // ----- ROOM STATUS PIE CHART -----
        // grafik status kamar
        $this->roomStatusChart = Rooms::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->toArray();

        // ----- 7-DAY BOOKING TREND -----
        // trend booking 7 hari
        $this->bookingTrend = Bookings::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // ----- LATEST TABLES -----
        // DATA TERBARU
        $this->latestBookings = Bookings::latest()->limit(5)->get();
        $this->latestComplaints = Complaints::latest()->limit(5)->get();
        $this->pendingPaymentProofs = PaymentProofs::where('status', 'pending')
            ->with(['user', 'bill.booking.room'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}