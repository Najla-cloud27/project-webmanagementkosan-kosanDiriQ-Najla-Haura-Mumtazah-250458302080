<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentProofs;
use App\Models\Bills;

class PaymentProofVerification extends Component
{
    use WithPagination;

    public $selectedProof;
    public $admin_notes;

    protected $paginationTheme = 'bootstrap';

    public function selectProof($proofId)
    {
        $this->selectedProof = PaymentProofs::with(['user', 'bill.booking.room'])->findOrFail($proofId);
    }

    public function approveProof()
    {
        $this->selectedProof->update([
            'status' => 'approved',
            'notes' => $this->admin_notes,
            'verified_at' => now(),
        ]);

        $this->selectedProof->bill->update([
            'status' => 'dibayar',
            'payment_date' => now(),
        ]);

        // Update booking status to dikonfirmasi
        // perubahan booking
        if ($this->selectedProof->bill->booking) {
            $this->selectedProof->bill->booking->update([
                'status' => 'dikonfirmasi',
            ]);
            
            // Update room status to sudah_dipesan
            // perubahan kamar
            $this->selectedProof->bill->booking->room->update([
                'status' => 'sudah_dipesan',
            ]);
        }

        session()->flash('message', 'Bukti pembayaran disetujui!');
        $this->reset(['selectedProof', 'admin_notes']);
    }

    public function rejectProof()
    {
        $this->validate([
            'admin_notes' => 'required|string',
        ]);

        $this->selectedProof->update([
            'status' => 'rejected',
            'notes' => $this->admin_notes,
            'verified_at' => now(),
        ]);

        $this->selectedProof->bill->update([
            'status' => 'belum_dibayar',
        ]);

        // Update booking status back to pending
        if ($this->selectedProof->bill->booking) {
            $this->selectedProof->bill->booking->update([
                'status' => 'pending',
            ]);
        }

        session()->flash('message', 'Bukti pembayaran ditolak!');
        $this->reset(['selectedProof', 'admin_notes']);
    }

    public function closeModal()
    {
        $this->reset(['selectedProof', 'admin_notes']);
    }

    public $search = '';
    public $statusFilter = '';

    public function render()
    {
        $paymentProofs = PaymentProofs::when($this->search, function ($query) {
                $query->where('payment_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('bill', function ($q) {
                        $q->where('bill_code', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['user', 'bill'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payment-proof-verification', compact('paymentProofs'));
    }
}