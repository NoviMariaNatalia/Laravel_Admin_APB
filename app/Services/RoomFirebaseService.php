<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\FirebaseRoom;
use Illuminate\Support\Facades\Storage;

class RoomFirebaseService
{
    protected $projectId;
    protected $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'fir-booking-b015e');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Mengambil semua data ruang dari Firebase
     */
    public function getAllRooms()
    {
        try {
            $response = Http::get($this->baseUrl . '/rooms');
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseFirestoreResponse($data);
            }
            
            \Log::error('Firebase getAllRooms failed: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            \Log::error('Firebase getAllRooms error: ' . $e->getMessage());
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

    /**
     * Menyimpan ruang baru ke Firebase
     */
    public function createRoom(array $roomData)
    {
        try {
            \Log::info('🔵 Creating new room in Firebase', $roomData);
            
            // Generate ID unik untuk room
            $roomId = uniqid('room_');
            
            // Prepare data untuk Firebase
            $firebaseData = [
                'fields' => [
                    'name' => ['stringValue' => $roomData['name']],
                    'building' => ['stringValue' => $roomData['building']],
                    'floor' => ['integerValue' => (string)$roomData['floor']],
                    'capacity' => ['integerValue' => (string)$roomData['capacity']],
                    'facilities' => [
                        'arrayValue' => [
                            'values' => array_map(function($facility) {
                                return ['stringValue' => $facility];
                            }, $roomData['facilities'])
                        ]
                    ],
                    'photo_url' => ['stringValue' => $roomData['photo_url'] ?? ''],
                    'createdAt' => ['timestampValue' => now()->toISOString()],
                    'updatedAt' => ['timestampValue' => now()->toISOString()],
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->patch($this->baseUrl . "/rooms/{$roomId}", $firebaseData);
            
            \Log::info("📡 Firebase createRoom response: " . $response->status() . " - " . $response->body());
            
            if ($response->successful()) {
                return $roomId;
            }
            
            return false;
            
        } catch (\Exception $e) {
            \Log::error('❌ Firebase createRoom error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update ruang di Firebase
     */
    public function updateRoom(string $roomId, array $roomData)
    {
        try {
            \Log::info("🔵 Updating room {$roomId} in Firebase", $roomData);
            
            // Prepare update data
            $updateFields = [
                'name' => ['stringValue' => $roomData['name']],
                'building' => ['stringValue' => $roomData['building']],
                'floor' => ['integerValue' => (string)$roomData['floor']],
                'capacity' => ['integerValue' => (string)$roomData['capacity']],
                'facilities' => [
                    'arrayValue' => [
                        'values' => array_map(function($facility) {
                            return ['stringValue' => $facility];
                        }, $roomData['facilities'])
                    ]
                ],
                'updatedAt' => ['timestampValue' => now()->toISOString()],
            ];

            // Add photo_url only if provided
            if (isset($roomData['photo_url'])) {
                $updateFields['photo_url'] = ['stringValue' => $roomData['photo_url']];
            }

            $firebaseData = ['fields' => $updateFields];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->patch($this->baseUrl . "/rooms/{$roomId}", $firebaseData);
            
            \Log::info("📡 Firebase updateRoom response: " . $response->status() . " - " . $response->body());
            
            return $response->successful();
            
        } catch (\Exception $e) {
            \Log::error('❌ Firebase updateRoom error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus ruang dari Firebase
     */
    public function deleteRoom(string $roomId)
    {
        try {
            \Log::info("🔵 Deleting room {$roomId} from Firebase");
            
            $response = Http::delete($this->baseUrl . "/rooms/{$roomId}");
            
            \Log::info("📡 Firebase deleteRoom response: " . $response->status() . " - " . $response->body());
            
            return $response->successful();
            
        } catch (\Exception $e) {
            \Log::error('❌ Firebase deleteRoom error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse response dari Firestore untuk multiple rooms
     */
    private function parseFirestoreResponse($data)
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

    /**
     * Upload foto ke storage dan return path
     */
    public function uploadRoomPhoto($file)
    {
        try {
            if ($file && $file->isValid()) {
                $filePath = $file->store('uploads/rooms', 'public');
                \Log::info('Room photo uploaded: ' . $filePath);
                return $filePath;
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('Error uploading room photo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Hapus foto dari storage
     */
    public function deleteRoomPhoto($photoPath)
    {
        try {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
                \Log::info('Room photo deleted: ' . $photoPath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('Error deleting room photo: ' . $e->getMessage());
            return false;
        }
    }
}