@extends('layouts.main_admin')

@section('title', 'Input Ruang')

@section('content')
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('admin.index_admin') }}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.create_room') }}">
                    <i class="bi bi-building"></i>
                    <span>Input Data Ruang</span>
                </a>
            </li><!-- End Input Data Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('admin.display_room') }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Edit Data Ruang</span>
                </a>
            </li><!-- End Edit Data Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('admin.bookings.history') }}">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span>Riwayat</span>
                </a>
            </li><!-- End Riwayat Nav -->
        </ul>
    </aside><!-- End Sidebar -->

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Tambah Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index_admin') }}">Beranda</a></li>
                <li class="breadcrumb-item">Tambah Data</li>
                <li class="breadcrumb-item active">Data Ruang</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Formulir Tambah Data Ruang</h5>
                        <p class="text-muted">Data ruang akan disimpan ke Firebase Firestore</p>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success')}}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error')}}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <form method="POST" action="{{ route('admin.store_room') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="building" class="form-label">Nama Gedung</label>
                                    <input type="text" name="building" class="form-control" id="building" 
                                           value="{{ old('building') }}" 
                                           placeholder="Contoh: Gedung A, Gedung Rektorat, dll" required>
                                    <small class="text-muted">Masukkan nama gedung secara manual</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="floor" class="form-label">Lantai</label>
                                    <input type="number" name='floor' class="form-control" id='floor' 
                                           value="{{ old('floor') }}" 
                                           placeholder="Contoh: 1, 2, 3" min="1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Ruangan</label>
                                <input type="text" name="name" class="form-control" id="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Contoh: Ruang 101, Lab Komputer, Aula Utama" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Kapasitas</label>
                                <div class="input-group">
                                    <input type="number" name="capacity" class="form-control" id="capacity" 
                                           value="{{ old('capacity') }}" 
                                           placeholder="50" min="1" required>
                                    <span class="input-group-text">orang</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="facilities" class="form-label">Fasilitas</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="AC" id="ac">
                                            <label class="form-check-label" for="ac">AC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="Proyektor" id="projector">
                                            <label class="form-check-label" for="projector">Proyektor</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="LCD" id="lcd">
                                            <label class="form-check-label" for="lcd">LCD</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="Papan Tulis" id="board">
                                            <label class="form-check-label" for="board">Papan Tulis</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="WiFi" id="wifi">
                                            <label class="form-check-label" for="wifi">WiFi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="Sound System" id="sound">
                                            <label class="form-check-label" for="sound">Sound System</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]" value="Meja Kursi" id="desk">
                                            <label class="form-check-label" for="desk">Meja Kursi</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="photo_url" class="form-label">Unggah Foto Ruangan</label>
                                <input type="file" name="photo_url" class="form-control" id="photo_url" 
                                       accept="image/jpeg,image/jpg,image/png" required>
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan ke Firebase
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </form><!-- End General Form Elements -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<script>
// Preview foto sebelum upload
document.getElementById('photo_url').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Cek ukuran file
        if (file.size > 2048 * 1024) { // 2MB
            alert('Ukuran file terlalu besar. Maksimal 2MB');
            e.target.value = '';
            return;
        }
        
        // Preview image (optional)
        const reader = new FileReader();
        reader.onload = function(e) {
            // Bisa tambahkan preview image di sini jika diperlukan
        };
        reader.readAsDataURL(file);
    }
});

// Auto-capitalize nama gedung dan ruangan
document.getElementById('building').addEventListener('input', function(e) {
    let value = e.target.value;
    e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
});

document.getElementById('name').addEventListener('input', function(e) {
    let value = e.target.value;
    e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
});
</script>
@endsection