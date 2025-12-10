<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'bill_code',
        'description',
        'total_amount',
        'due_date',
        'payment_date',
        'payment_method',
        'status',
    ];

    /**
     * Tagihan ini milik satu user.
     * Relasi: bills (∞) --- (1) users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tagihan ini terkait dengan satu booking.
     * Relasi: bills (∞) --- (1) bookings
     */
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    /**
     * Satu bill bisa punya banyak bukti pembayaran.
     * Relasi: bills (1) --- (∞) payment_proofs
     */
    public function paymentProofs()
    {
        return $this->hasMany(Paymentproofs::class, 'bill_id');
    }
}