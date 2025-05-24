<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::all();
        return view('admin.index_admin', compact('buildings'));
    }
    // Menampilkan form tambah gedung
    public function create()
    {
        $building = Building::first();
        $buildings = Building::all();
        return view('admin.form_gedung', compact('building', 'buildings'));
    }

    // Menyimpan data gedung ke database
    public function store(Request $request)
    {
        $request->validate([
            'name_building' => 'required|string|max:255',
            'floor' => 'required|integer|min:1',
            'mapping' => 'required|url',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);        

        // Upload gambar
        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/img'), $filename);

        // Simpan data gedung
        Building::create([
            'name_building' => $request->name_building,
            'floor' => $request->floor,
            'mapping' => $request->mapping,
            'foto' => $filename,
        ]);

        return redirect()->route('mahasiswa.index_mhs')->with('success', 'Data Gedung berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $building = Building::findOrFail($id);
        $allBuildings = Building::all();
        return view('admin.edit_gedung', compact('building', 'allBuildings'));
    }

    public function formGedung()
    {
        $building = Building::all(); // Atau sesuai kebutuhan Anda
        return view('admin.form_gedung', compact('building'));
    }

    public function updateGedung(Request $request, $id)
{
    $validated = $request->validate([
        'building_id' => 'required|exists:buildings,id',
        'name_building' => 'required|string|max:255',
        'floor' => 'required|integer|min:1',
        'mapping' => 'required|url',
        'foto' => 'nullable|image|max:2048',
    ]);

    // Menemukan gedung berdasarkan building_id
    $building = Building::findOrFail($request->building_id);
    
    $building->name_building = $validated['name_building'];
    $building->floor = $validated['floor'];
    $building->mapping = $validated['mapping'];

    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/img'), $filename);
        $building->foto = $filename;
    }

    $building->save();

    return redirect()->route('admin.edit_gedung', ['id' => $building->id])
                     ->with('success', 'Data gedung berhasil diperbarui.');
}


    public function destroy($id)
    {
        $building = Building::findOrFail($id);
        $building->delete();

        return redirect()->route('admin.index_admin')->with('success', 'Gedung berhasil dihapus');
    }

    public function hapusGedung($id)
    {
        $building = Building::findOrFail($id); // Mengambil data gedung berdasarkan ID
        $allBuildings = Building::all(); // Opsional, untuk menampilkan semua gedung
        return view('admin.hapus_gedung', compact('building', 'allBuildings'));
    }
}
