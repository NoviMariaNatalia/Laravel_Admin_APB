<?php

namespace App\Models;

class FirebaseBooking
{
    public $id;
    public $nama;
    public $noHp;
    public $ruangan;
    public $tanggalMulai;
    public $tanggalSelesai;
    public $jamMulai;
    public $jamSelesai;
    public $tujuan;
    public $organisasi;
    public $status;
    public $createdAt;
    public $updatedAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->nama = $data['nama'] ?? '';
        $this->noHp = $data['noHp'] ?? '';
        $this->ruangan = $data['ruangan'] ?? '';
        $this->tanggalMulai = $data['tanggalMulai'] ?? '';
        $this->tanggalSelesai = $data['tanggalSelesai'] ?? '';
        $this->jamMulai = $data['jamMulai'] ?? '';
        $this->jamSelesai = $data['jamSelesai'] ?? '';
        $this->tujuan = $data['tujuan'] ?? '';
        $this->organisasi = $data['organisasi'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }
}