<?php

namespace App\Models;

class FirebaseRoom
{
    public $id;
    public $name;
    public $building;  // Nama gedung langsung (denormalized)
    public $floor;
    public $capacity;
    public $facilities;
    public $photo_url;
    public $createdAt;
    public $updatedAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->building = $data['building'] ?? '';
        $this->floor = $data['floor'] ?? 0;
        $this->capacity = $data['capacity'] ?? 0;
        $this->facilities = $data['facilities'] ?? [];
        $this->photo_url = $data['photo_url'] ?? '';
        $this->createdAt = $data['createdAt'] ?? now();
        $this->updatedAt = $data['updatedAt'] ?? now();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'building' => $this->building,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'facilities' => $this->facilities,
            'photo_url' => $this->photo_url,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    // Helper method untuk format fasilitas sebagai string
    public function getFacilitiesString()
    {
        return is_array($this->facilities) ? implode(', ', $this->facilities) : $this->facilities;
    }

    // Helper method untuk check apakah memiliki fasilitas tertentu
    public function hasFacility($facility)
    {
        return is_array($this->facilities) ? in_array($facility, $this->facilities) : false;
    }
}