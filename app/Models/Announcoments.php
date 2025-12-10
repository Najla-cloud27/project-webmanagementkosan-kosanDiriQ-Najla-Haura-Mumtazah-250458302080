<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcoments extends Model
{
     protected $fillable = [
        'admin_id',
        'title',
        'content',
        'publish_status',
        'image_url',
    ];

    /**
     * Pengumuman ini dibuat oleh satu user (biasanya admin).
     * Relasi: announcements (∞) --- (1) users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}