<?php

namespace App\Services;

use App\Models\FirebaseBooking;

class BookingFirebaseService
{
    protected $firebaseService;
    protected $collection;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
        $this->collection = $firebaseService->getCollection('bookings');
    }

    public function getAllBookings()
    {
        try {
            $documents = $this->collection->orderBy('createdAt', 'DESC')->documents();
            $bookings = [];

            foreach ($documents as $document) {
                $data = $document->data();
                $data['id'] = $document->id();
                
                // Convert Firebase timestamp
                if (isset($data['createdAt'])) {
                    $data['createdAt'] = $data['createdAt']->get()->toDateTime();
                }
                
                $bookings[] = new FirebaseBooking($data);
            }

            return $bookings;
        } catch (\Exception $e) {
            \Log::error('Error getting all bookings: ' . $e->getMessage());
            return [];
        }
    }

    public function getPendingBookings()
    {
        try {
            $documents = $this->collection
                ->where('status', '=', 'pending')
                ->orderBy('createdAt', 'DESC')
                ->documents();
            
            $bookings = [];

            foreach ($documents as $document) {
                $data = $document->data();
                $data['id'] = $document->id();
                
                if (isset($data['createdAt'])) {
                    $data['createdAt'] = $data['createdAt']->get()->toDateTime();
                }
                
                $bookings[] = new FirebaseBooking($data);
            }

            return $bookings;
        } catch (\Exception $e) {
            \Log::error('Error getting pending bookings: ' . $e->getMessage());
            return [];
        }
    }

    public function updateBookingStatus(string $bookingId, string $status)
    {
        try {
            $this->collection->document($bookingId)->update([
                [
                    'path' => 'status',
                    'value' => $status
                ],
                [
                    'path' => 'updatedAt',
                    'value' => now()->toISOString()
                ]
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating booking status: ' . $e->getMessage());
            return false;
        }
    }

    public function getBookingById(string $bookingId)
    {
        try {
            $document = $this->collection->document($bookingId)->snapshot();
            
            if ($document->exists()) {
                $data = $document->data();
                $data['id'] = $document->id();
                
                if (isset($data['createdAt'])) {
                    $data['createdAt'] = $data['createdAt']->get()->toDateTime();
                }
                
                return new FirebaseBooking($data);
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting booking by ID: ' . $e->getMessage());
            return null;
        }
    }
}