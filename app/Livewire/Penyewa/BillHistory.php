<?php

namespace App\Livewire\Penyewa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bills;
use Illuminate\Support\Facades\Auth;

class BillHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFrom;
    public $dateTo;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $bills = Bills::where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('bill_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->with(['booking', 'paymentProofs'])
            ->latest()
            ->paginate(10);

        $totalPaid = Bills::where('user_id', Auth::id())
            ->where('status', 'dibayar')
            ->sum('total_amount');

        $totalUnpaid = Bills::where('user_id', Auth::id())
            ->where('status', 'belum_dibayar')
            ->sum('total_amount');

        return view('livewire.penyewa.bill-history', compact('bills', 'totalPaid', 'totalUnpaid'));
    }
}