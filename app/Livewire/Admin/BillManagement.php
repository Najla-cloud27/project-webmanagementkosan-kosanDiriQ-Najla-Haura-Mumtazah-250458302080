<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bills;
use App\Models\User;
use App\Models\Bookings;

class BillManagement extends Component
{
    use WithPagination;

     // filter & pencarian
    public $search = '';
    public $statusFilter = '';
    
    // modal tambah tagihan
    public $showModal = false;   
    
    // Form create bill
    public $booking_id;
    public $user_id;
    public $total_amount;
    public $due_date;
    public $description;

    protected $paginationTheme = 'bootstrap';

    // Aturan Validasi
    protected $rules = [
        'booking_id' => 'required|exists:bookings,id',
        'total_amount' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'description' => 'nullable|string',
    ];

    public function createBill()
    {
        $this->validate();

        $booking = Bookings::findOrFail($this->booking_id);

        Bills::create([
            'user_id' => $booking->user_id,
            'booking_id' => $this->booking_id,
            'bill_code' => 'BILL-' . time() . rand(1000, 9999),
            'total_amount' => $this->total_amount,
            'due_date' => $this->due_date,
            'description' => $this->description,
            'status' => 'belum_dibayar',
        ]);

        session()->flash('success', 'Tagihan berhasil dibuat!');
        $this->reset(['booking_id', 'amount', 'due_date', 'description']);
    }

    public $showDetailModal = false;
    public $selectedBill;

    public function viewDetail($id)
    {
        $this->selectedBill = Bills::with(['user', 'booking'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedBill = null;
    }

    public function delete($id)
    {
        Bills::find($id)->delete();
        session()->flash('message', 'Tagihan berhasil dihapus.');
    }

    public function render()
    {
        $bills = Bills::when($this->search, function ($query) {
                $query->where('bill_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['user', 'booking'])
            ->latest()
            ->paginate(10);

        $bookings = Bookings::where('status', 'dikonfirmasi')->get();

        return view('livewire.admin.bill-management', compact('bills', 'bookings'));
    }
}