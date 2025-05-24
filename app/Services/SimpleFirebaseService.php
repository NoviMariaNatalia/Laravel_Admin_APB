<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\FirebaseBooking;
use App\Models\FirebaseRoom;

class SimpleFirebaseService
{
    protected $projectId;
    protected $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'fir-booking-b015e');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    // ========== BOOKING METHODS ==========
    
    public function getAllBookings()
    {
        try {
            $response = Http::get($this->baseUrl . '/bookings');
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseFirestoreBookingResponse($data);
            }
            
            \Log::error('Firebase HTTP request failed: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            \Log::error('Firebase HTTP error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPendingBookings()
    {
        try {
            // Firebase REST API query for status = pending
            $url = $this->baseUrl . '/bookings';
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $allBookings = $this->parseFirestoreBookingResponse($data);
                
                // Filter pending bookings
                return array_filter($allBookings, function($booking) {
                    return $booking->status === 'pending';
                });
            }
            
            return [];
        } catch (\Exception $e) {
            \Log::error('Firebase pending bookings error: ' . $e->getMessage());
            return [];
        }
    }

    public function updateBookingStatus(string $bookingId, string $status)
    {
        try {
            \Log::info("🔵 Updating booking {$bookingId} to status: {$status}");
            
            // Firebase REST API - Use field-specific update
            $url = $this->baseUrl . "/bookings/{$bookingId}";
            
            // HANYA update field status dan updatedAt (jangan overwrite semua)
            $updateData = [
                'fields' => [
                    'status' => [
                        'stringValue' => $status
                    ],
                    'updatedAt' => [
                        'stringValue' => now()->toISOString()
                    ]
                ]
            ];
            
            // Use updateMask untuk HANYA update field tertentu
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->patch($url . '?updateMask.fieldPaths=status&updateMask.fieldPaths=updatedAt', $updateData);
            
            \Log::info("📡 Firebase response: " . $response->status() . " - " . $response->body());
            
            return $response->successful();
            
        } catch (\Exception $e) {
            \Log::error('❌ Firebase update error: ' . $e->getMessage());
            return false;
        }
    }

    // ========== ROOM METHODS ==========
    
    /**
     * Mengambil semua data ruang dari Firebase
     */
    public function getAllRooms()
    {
        try {
            $response = Http::get($this->baseUrl . '/rooms');
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseFirestoreRoomResponse($data);
            }
            
            \Log::error('Firebase getAllRooms failed: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            \Log::error('Firebase getAllRooms error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil ruang berdasarkan gedung
     */
    public function getRoomsByBuilding(string $building)
    {
        try {
            $allRooms = $this->getAllRooms();
            
            // Filter berdasarkan nama gedung
            return array_filter($allRooms, function($room) use ($building) {
                return strtolower($room->building) === strtolower($building);
            });
        } catch (\Exception $e) {
            \Log::error('Firebase getRoomsByBuilding error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil data ruang berdasarkan ID
     */
    public function getRoomById(string $roomId)
    {
        try {
            $response = Http::get($this->baseUrl . "/rooms/{$roomId}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseFirestoreRoom($data);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Firebase getRoomById error: ' . $e->getMessage());
            return null;
        }
    }

    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Parse response dari Firestore untuk bookings
     */
    private function parseFirestoreBookingResponse($data)
    {
        $bookings = [];
        
        if (isset($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                $fields = $doc['fields'] ?? [];
                
                $bookingData = [
                    'id' => basename($doc['name']),
                    'nama' => $fields['nama']['stringValue'] ?? '',
                    'noHp' => $fields['noHp']['stringValue'] ?? '',
                    'ruangan' => $fields['ruangan']['stringValue'] ?? '',
                    'tanggalMulai' => $fields['tanggalMulai']['stringValue'] ?? '',
                    'tanggalSelesai' => $fields['tanggalSelesai']['stringValue'] ?? '',
                    'jamMulai' => $fields['jamMulai']['stringValue'] ?? '',
                    'jamSelesai' => $fields['jamSelesai']['stringValue'] ?? '',
                    'tujuan' => $fields['tujuan']['stringValue'] ?? '',
                    'organisasi' => $fields['organisasi']['stringValue'] ?? '',
                    'status' => $fields['status']['stringValue'] ?? 'pending',
                    'createdAt' => isset($fields['createdAt']['timestampValue']) 
                        ? \Carbon\Carbon::parse($fields['createdAt']['timestampValue'])
                        : now(),
                    'updatedAt' => $fields['updatedAt']['stringValue'] ?? null,
                ];
                
                $bookings[] = new FirebaseBooking($bookingData);
            }
        }
        
        return $bookings;
    }

    /**
     * Parse response dari Firestore untuk rooms
     */
    private function parseFirestoreRoomResponse($data)
    {
        $rooms = [];
        
        if (isset($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                $room = $this->parseFirestoreRoom($doc);
                if ($room) {
                    $rooms[] = $room;
                }
            }
        }
        
        return $rooms;
    }

    /**
     * Parse single room document dari Firestore
     */
    private function parseFirestoreRoom($doc)
    {
        try {
            $fields = $doc['fields'] ?? [];
            
            $facilities = [];
            if (isset($fields['facilities']['arrayValue']['values'])) {
                foreach ($fields['facilities']['arrayValue']['values'] as $facility) {
                    $facilities[] = $facility['stringValue'] ?? '';
                }
            }
            
            $roomData = [
                'id' => basename($doc['name']),
                'name' => $fields['name']['stringValue'] ?? '',
                'building' => $fields['building']['stringValue'] ?? '',
                'floor' => (int)($fields['floor']['integerValue'] ?? 0),
                'capacity' => (int)($fields['capacity']['integerValue'] ?? 0),
                'facilities' => $facilities,
                'photo_url' => $fields['photo_url']['stringValue'] ?? '',
                'createdAt' => isset($fields['createdAt']['timestampValue']) 
                    ? \Carbon\Carbon::parse($fields['createdAt']['timestampValue'])
                    : now(),
                'updatedAt' => isset($fields['updatedAt']['timestampValue'])
                    ? \Carbon\Carbon::parse($fields['updatedAt']['timestampValue'])
                    : now(),
            ];
            
            return new FirebaseRoom($roomData);
            
        } catch (\Exception $e) {
            \Log::error('Error parsing Firestore room: ' . $e->getMessage());
            return null;
        }
    }
}