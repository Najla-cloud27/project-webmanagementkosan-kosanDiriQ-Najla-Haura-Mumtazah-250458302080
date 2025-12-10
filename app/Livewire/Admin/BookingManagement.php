<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bookings;
use App\Models\Rooms;
use App\Models\User;

class BookingManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDetailModal = false;
    public $search = '';
    public $statusFilter = '';
    public $bookingId;
    public $selectedBooking;
    
    // Form fields
    public $user_id, $room_id, $duration_in_months, $planned_check_in_date, $notes;
    public $editMode = false;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'room_id' => 'required|exists:rooms,id',
        'duration_in_months' => 'required|integer|min:1',
        'planned_check_in_date' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->user_id = '';
        $this->room_id = '';
        $this->duration_in_months = 1;
        $this->planned_check_in_date = '';
        $this->notes = '';
        $this->editMode = false;
        $this->bookingId = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $room = Rooms::findOrFail($this->room_id);
        $totalPrice = $room->price * $this->duration_in_months;
        
        // Calculate check out date
        $checkInDate = \Carbon\Carbon::parse($this->planned_check_in_date);
        $checkOutDate = $checkInDate->copy()->addMonths((int) $this->duration_in_months);

        $data = [
            'user_id' => $this->user_id,
            'room_id' => $this->room_id,
            'duration_in_months' => $this->duration_in_months,
            'total_price' => $totalPrice,
            'planned_check_in_date' => $this->planned_check_in_date,
            'selesai_booking' => $checkOutDate->format('Y-m-d'),
            'notes' => $this->notes,
            'status' => 'pending',
        ];

        if ($this->editMode) {
            $booking = Bookings::find($this->bookingId);
            
            // Don't update booking_code when editing
            $booking->update($data);
            session()->flash('message', 'Booking berhasil diupdate.');
        } else {
            // Add booking_code only for new booking
            $data['booking_code'] = 'BOOK-' . time() . rand(1000, 9999);
            Bookings::create($data);
            session()->flash('message', 'Booking berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $booking = Bookings::with('room')->findOrFail($id);
        $this->bookingId = $id;
        $this->user_id = $booking->user_id;
        $this->room_id = $booking->room_id;
        $this->duration_in_months = $booking->duration_in_months;
        // Format date for input type="date" (YYYY-MM-DD)
        $this->planned_check_in_date = $booking->planned_check_in_date ? 
            \Carbon\Carbon::parse($booking->planned_check_in_date)->format('Y-m-d') : '';
        $this->notes = $booking->notes;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function viewDetail($id)
    {
        $this->selectedBooking = Bookings::with(['user', 'room'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedBooking = null;
    }

    public function updateStatus($id, $status)
    {
        $booking = Bookings::findOrFail($id);
        $booking->update(['status' => $status]);
        
        if ($status == 'dikonfirmasi') {
            // Update room status to sudah_dipesan
            $booking->room->update(['status' => 'sudah_dipesan']);
            
            // Auto-create bill for confirmed booking (only if not exists)
            if (!$booking->bill) {
                // Jika tagiha belum ada
                \App\Models\Bills::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'bill_code' => 'BILL-' . time() . rand(1000, 9999),
                    'total_amount' => $booking->total_price,
                    'due_date' => now()->addDays(3),
                    'description' => 'Pembayaran booking kamar ' . $booking->room->name,
                    'status' => 'dibayar',
                    'payment_date' => now(),
                ]);
            } else {
                // If bill exists, update status to dibayar
                // Jika tagihan sudh ada
                $booking->bill->update([
                    'status' => 'dibayar',
                    'payment_date' => now(),
                ]);
            }
        } elseif ($status == 'dibatalkan') {
            // Return room to available
            // jika booking dibatalkan
            $booking->room->update(['status' => 'tersedia']);
        }
        
        session()->flash('message', 'Status booking berhasil diupdate.');
    }

    public function delete($id)
    {
        Bookings::find($id)->delete();
        session()->flash('message', 'Booking berhasil dihapus.');
    }

    public function render()
    {
        $bookings = Bookings::with(['user', 'room'])
            ->when($this->search, function ($query) {
                $query->where('booking_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('room', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

            // Data Penyewa
        $users = User::where('role', 'penyewa')->get();
        
        // Get available rooms + current room if editing
        // Data Kamar
        $rooms = Rooms::where('status', 'tersedia')
            ->when($this->editMode && $this->room_id, function($query) {
                $query->orWhere('id', $this->room_id);
            })
            ->get();

        return view('livewire.admin.booking-management', [
            'bookings' => $bookings,
            'users' => $users,
            'rooms' => $rooms,
        ]);
    }
}