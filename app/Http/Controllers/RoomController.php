<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoomFirebaseService;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    protected $roomFirebaseService;

    public function __construct(RoomFirebaseService $roomFirebaseService)
    {
        $this->roomFirebaseService = $roomFirebaseService;
    }

    /**
     * Menampilkan halaman form input room
     */
    public function input()
    {
        return view('admin.form_ruang');
    }

    /**
     * Menambahkan room baru ke Firebase
     */
    public function store(Request $request)
    {
        \Log::info('🔵 Masuk ke store() method untuk Firebase.');

        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|integer',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'facilities' => 'required|array',
            'photo_url' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        \Log::info('✅ Validasi berhasil.');

        try {
            // Upload foto
            $photoPath = null;
            if ($request->hasFile('photo_url')) {
                $photoPath = $this->roomFirebaseService->uploadRoomPhoto($request->file('photo_url'));
                if (!$photoPath) {
                    return redirect()->back()->with('error', 'Gagal mengupload foto.');
                }
            }

            // Prepare data untuk Firebase
            $roomData = [
                'name' => $request->input('name'),
                'building' => $request->input('building'),
                'floor' => (int)$request->input('floor'),
                'capacity' => (int)$request->input('capacity'),
                'facilities' => $request->input('facilities'),
                'photo_url' => $photoPath ?? '',
            ];

            // Simpan ke Firebase
            $roomId = $this->roomFirebaseService->createRoom($roomData);

            if ($roomId) {
                \Log::info('✅ Data ruang berhasil disimpan ke Firebase dengan ID: ' . $roomId);
                return redirect()->route('admin.create_room')->with('success', 'Ruang berhasil ditambahkan ke Firebase.');
            } else {
                // Hapus foto jika gagal simpan ke Firebase
                if ($photoPath) {
                    $this->roomFirebaseService->deleteRoomPhoto($photoPath);
                }
                return redirect()->back()->with('error', 'Gagal menyimpan data ruang ke Firebase.');
            }

        } catch (\Exception $e) {
            \Log::error('❌ Error saat menyimpan ruang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan semua room dari Firebase
     */
    public function display()
    {
        try {
            $rooms = $this->roomFirebaseService->getAllRooms();
            \Log::info('📋 Berhasil mengambil ' . count($rooms) . ' ruang dari Firebase');
            return view('admin.edit_ruang', compact('rooms'));
        } catch (\Exception $e) {
            \Log::error('❌ Error mengambil data ruang: ' . $e->getMessage());
            return view('admin.edit_ruang', ['rooms' => []]);
        }
    }

    /**
     * Menampilkan halaman form edit room
     */
    public function edit($id)
    {
        try {
            $room = $this->roomFirebaseService->getRoomById($id);
            
            if (!$room) {
                return redirect()->route('admin.display_room')->with('error', 'Ruangan tidak ditemukan.');
            }

            return view('admin.form_edit', compact('room'));
        } catch (\Exception $e) {
            \Log::error('❌ Error mengambil data ruang untuk edit: ' . $e->getMessage());
            return redirect()->route('admin.display_room')->with('error', 'Terjadi kesalahan saat mengambil data ruang.');
        }
    }

    /**
     * Memperbarui room di Firebase
     */
    public function update(Request $request, $id)
    {
        \Log::info("🔵 Masuk ke update() method untuk room ID: {$id}");

        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|integer',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'facilities' => 'required|array',
        ]);

        try {
            // Ambil data ruang saat ini
            $currentRoom = $this->roomFirebaseService->getRoomById($id);
            if (!$currentRoom) {
                return redirect()->route('admin.display_room')->with('error', 'Ruangan tidak ditemukan.');
            }

            // Prepare update data
            $updateData = [
                'name' => $request->input('name'),
                'building' => $request->input('building'),
                'floor' => (int)$request->input('floor'),
                'capacity' => (int)$request->input('capacity'),
                'facilities' => $request->input('facilities'),
            ];

            // Handle foto baru jika ada
            if ($request->hasFile('photo_url')) {
                $request->validate([
                    'photo_url' => 'image|mimes:jpg,jpeg,png|max:2048'
                ]);

                // Upload foto baru
                $newPhotoPath = $this->roomFirebaseService->uploadRoomPhoto($request->file('photo_url'));
                if ($newPhotoPath) {
                    // Hapus foto lama jika ada
                    if ($currentRoom->photo_url) {
                        $this->roomFirebaseService->deleteRoomPhoto($currentRoom->photo_url);
                    }
                    $updateData['photo_url'] = $newPhotoPath;
                }
            }

            // Update di Firebase
            $success = $this->roomFirebaseService->updateRoom($id, $updateData);

            if ($success) {
                \Log::info("✅ Room {$id} berhasil diupdate di Firebase");
                return redirect()->route('admin.edit_room', $id)->with('success', 'Ruang berhasil diperbarui.');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui data ruang di Firebase.');
            }

        } catch (\Exception $e) {
            \Log::error("❌ Error updating room {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus room dari Firebase
     */
    public function destroy($id)
    {
        try {
            \Log::info("🔵 Menghapus room ID: {$id} dari Firebase");

            // Ambil data ruang untuk hapus foto
            $room = $this->roomFirebaseService->getRoomById($id);
            
            // Hapus dari Firebase
            $success = $this->roomFirebaseService->deleteRoom($id);

            if ($success) {
                // Hapus foto jika ada
                if ($room && $room->photo_url) {
                    $this->roomFirebaseService->deleteRoomPhoto($room->photo_url);
                }

                \Log::info("✅ Room {$id} berhasil dihapus dari Firebase");
                return redirect()->route('admin.display_room')->with('success', 'Ruang berhasil dihapus.');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus ruang dari Firebase.');
            }

        } catch (\Exception $e) {
            \Log::error("❌ Error deleting room {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus ruang.');
        }
    }

    /**
     * Menampilkan daftar ruang berdasarkan gedung (untuk mahasiswa)
     * Method ini tetap ada jika diperlukan untuk web, tapi Flutter akan akses Firebase langsung
     */
    public function showRoomsByBuilding($building)
    {
        try {
            $rooms = $this->roomFirebaseService->getAllRooms();
            
            // Filter berdasarkan nama gedung
            $filteredRooms = array_filter($rooms, function($room) use ($building) {
                return strtolower($room->building) === strtolower($building);
            });

            return view('mahasiswa.daftarRuang_mhs', [
                'rooms' => $filteredRooms,
                'building' => (object)['name_building' => $building]
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error showRoomsByBuilding: ' . $e->getMessage());
            return view('mahasiswa.daftarRuang_mhs', [
                'rooms' => [],
                'building' => (object)['name_building' => $building]
            ]);
        }
    }
}