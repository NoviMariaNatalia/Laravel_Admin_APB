<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Building;

class BookingController extends Controller
{
    // Menampilkan daftar booking untuk admin
    public function adminIndex()
    {

        $buildings = Building::all();

        $bookings = Booking::with('room')
                          ->where('status', 'Pending')
                          ->orderBy('created_at', 'desc')
                          ->get();
        return view('admin.index_admin', compact('buildings', 'bookings'));
    }

    // Menampilkan riwayat booking untuk admin
    public function adminHistory()
    {
        $buildings = Building::all();

        $bookings = Booking::with('room')
                          ->whereIn('status', ['Disetujui', 'Ditolak'])
                          ->orderBy('updated_at', 'desc')
                          ->get();
        return view('admin.riwayat_admin', compact('buildings', 'bookings'));
    }

    // Menangani aksi approve/reject
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

}
