<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemesan'); 
            $table->string('no_hp'); 
            $table->foreignId('room_id')->constrained('rooms'); 
            $table->date('tgl_mulai'); 
            $table->date('tgl_selesai');
            $table->time('jam_mulai'); 
            $table->time('jam_selesai'); 
            $table->string('tujuan'); 
            $table->string('organisasi');
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
