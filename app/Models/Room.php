<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'building_id',
        'floor',
        'name',
        'capacity',
        'facilities',
        'photo_url'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
