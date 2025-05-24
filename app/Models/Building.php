<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    protected $fillable = [
        'id',
        'name_building',
        'floor',
        'mapping',
        'foto'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
