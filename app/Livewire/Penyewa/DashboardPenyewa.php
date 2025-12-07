<?php

namespace App\Livewire\Penyewa;

use Livewire\Component;
use App\Models\Bookings;
use App\Models\Bills;
use App\Models\Complaints;
use App\Models\Announcoments;

class DashboardPenyewa extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        // Get statistics
        $stats = [
            'activeBookings' => Bookings::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'unpaidBills' => Bills::where('user_id', $user->id)
                ->where('status', 'belum_dibayar')
                ->count(),
            'pendingComplaints' => Complaints::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'totalSpent' => Bills::where('user_id', $user->id)
                ->where('status', 'dibayar')
                ->sum('total_amount')
        ];
        
        // Get recent data
        $recentBills = Bills::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentAnnouncements = Announcoments::orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        $recentComplaints = Complaints::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        return view('livewire.penyewa.dashboard-penyewa', [
            'stats' => $stats,
            'recentBills' => $recentBills,
            'recentAnnouncements' => $recentAnnouncements,
            'recentComplaints' => $recentComplaints
        ]);
    }
}