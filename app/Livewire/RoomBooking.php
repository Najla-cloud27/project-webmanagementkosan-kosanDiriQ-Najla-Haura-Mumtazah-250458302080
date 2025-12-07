<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rooms;
use App\Models\Bookings;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoomBooking extends Component
{
    public $room;
    public $duration_in_months = 1;
    public $planned_check_in_date;
    public $notes;

    protected $rules = [
        'duration_in_months' => 'required|integer|min:1|max:12',
        'planned_check_in_date' => 'required|date|after_or_equal:today',
        'notes' => 'nullable|string',
    ];

    public function mount($roomId)
    {
        $this->room = Rooms::findOrFail($roomId);
        
        if ($this->room->status !== 'tersedia' || $this->room->stok < 1) {
            session()->flash('error', 'Kamar tidak tersedia untuk booking.');
            return redirect()->route('rooms.index');
        }

        $this->planned_check_in_date = Carbon::today()->addDays(1)->format('Y-m-d');
    }

    public function getTotalPriceProperty()
    {
        return $this->room->price * (int) $this->duration_in_months;
    }

    public function bookRoom()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return redirect()->route('login');
        }

        $this->validate();

        // Cast to integer to fix Carbon type error
        $durationMonths = (int) $this->duration_in_months;

        $checkInDate = Carbon::parse($this->planned_check_in_date);
        $checkOutDate = $checkInDate->copy()->addMonths($durationMonths);

        $booking = Bookings::create([
            'user_id' => Auth::id(),
            'room_id' => $this->room->id,
            'booking_code' => 'BOOK-' . time() . rand(1000, 9999),
            'duration_in_months' => $durationMonths,
            'planned_check_in_date' => $this->planned_check_in_date,
            'selesai_booking' => $checkOutDate->format('Y-m-d'),
            'total_price' => $this->totalPrice,
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        // Update room status to sudah_dipesan
        $this->room->update(['status' => 'sudah_dipesan']);

        session()->flash('success', 'Booking berhasil! Silakan lakukan pembayaran.');
        return redirect()->route('penyewa.payment', ['bookingId' => $booking->id]);
    }

    public function render()
    {
        return view('livewire.room-booking');
    }
}