<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'nama_pemesan',
        'no_hp',
        'room_id',
        'tgl_mulai',
        'tgl_selesai',
        'jam_mulai',
        'jam_selesai',
        'tujuan',
        'organisasi',
        'status'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
