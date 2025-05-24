<?php

namespace App\Http\Controllers;

use App\Services\SimpleFirebaseService; // Ganti import
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $bookingService;

    public function __construct(SimpleFirebaseService $bookingService) // Ganti dependency
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        try {
            $bookings = $this->bookingService->getPendingBookings();
            
            // DEBUG: Dump data structure
            if (request()->has('debug')) {
                dd([
                    'raw_bookings' => $bookings,
                    'first_booking' => $bookings[0] ?? null,
                    'booking_properties' => $bookings[0] ? get_object_vars($bookings[0]) : null
                ]);
            }
            
            $buildings = collect(); // Empty for now
            
            return view('admin.index_admin', compact('bookings', 'buildings'));
        } catch (\Exception $e) {
            \Log::error('AdminController index error: ' . $e->getMessage());
            
            return view('admin.index_admin', [
                'bookings' => collect(),
                'buildings' => collect(),
                'error' => 'Gagal memuat data booking: ' . $e->getMessage()
            ]);
        }
    }

    public function updateBookingStatus(Request $request, $id)
    {
        try {
            $status = $request->input('status');
            
            $firebaseStatus = match($status) {
                'Disetujui' => 'approved',
                'Ditolak' => 'rejected',
                default => 'pending'
            };

            $result = $this->bookingService->updateBookingStatus($id, $firebaseStatus);

            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            \Log::error('Update booking status error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function bookingsHistory()
    {
        try {
            // Get ALL bookings dari Firebase
            $allBookings = $this->bookingService->getAllBookings();
            
            // Debug jika diperlukan
            // if (request()->has('debug')) {
            //     dd([
            //         'all_bookings_count' => count($allBookings),
            //         'all_bookings' => $allBookings,
            //         'sample_booking' => $allBookings[0] ?? null
            //     ]);
            // }
            
            // Simple filter - langsung dalam foreach
            $processedBookings = [];
            
            foreach ($allBookings as $booking) {
                if ($booking->status === 'approved' || $booking->status === 'rejected') {
                    $processedBookings[] = $booking;
                }
            }
            
            $buildings = collect();
            
            return view('admin.riwayat_admin', [
                'bookings' => $processedBookings,
                'buildings' => $buildings
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Bookings history error: ' . $e->getMessage());
            
            return view('admin.riwayat_admin', [
                'bookings' => [],
                'buildings' => collect(),
                'error' => 'Gagal memuat riwayat booking: ' . $e->getMessage()
            ]);
        }
    }

    public function debugFirebase()
    {
        try {
            $rawBookings = $this->bookingService->getAllBookings();
            
            return response()->json([
                'status' => 'SUCCESS',
                'count' => count($rawBookings),
                'raw_data_sample' => $rawBookings[0] ?? null,
                'all_data' => $rawBookings
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}