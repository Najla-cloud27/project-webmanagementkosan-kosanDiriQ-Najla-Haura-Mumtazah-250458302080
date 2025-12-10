<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Complaints;

class ComplaintManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedComplaint;
    public $newStatus;
    public $adminNotes;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function selectComplaint($complaintId)
    {
        $this->selectedComplaint = Complaints::with(['user', 'room'])->findOrFail($complaintId);
        $this->newStatus = $this->selectedComplaint->status;
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:dikirim,diproses,ditolak,selesai',
        ]);

        $this->selectedComplaint->update([
            'status' => $this->newStatus,
        ]);

        session()->flash('success', 'Status keluhan berhasil diupdate!');
        $this->reset(['selectedComplaint', 'newStatus']);
    }

    public function render()
    {
        $complaints = Complaints::when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['user', 'room'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.complaint-management', compact('complaints'));
    }
}