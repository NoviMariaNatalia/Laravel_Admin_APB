@extends('layouts.main_admin')

@section('title', 'Form Edit Ruang')

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
                <a class="nav-link collapsed" href="{{ route('admin.create_room') }}">
                    <i class="bi bi-building"></i>
                    <span>Input Data Ruang</span>
                </a>
            </li><!-- End Input Data Nav -->

            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.display_room') }}">
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
    <h1>Edit Data</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.index_admin') }}">Beranda</a></li>
        <li class="breadcrumb-item">Edit Data</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.display_room') }}">Data Ruang</a></li>
        <li class="breadcrumb-item active">Form Edit Ruang</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-cloud-check text-success"></i> 
              Formulir Edit Data Ruang (Firebase)
            </h5>
            <p class="text-muted">ID Firebase: <code>{{ $room->id }}</code></p>

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

              <form action="{{ route('admin.update_room', $room->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label for="building" class="form-label">Nama Gedung</label>
                      <input type="text" name="building" class="form-control" id="building"
                             value="{{ old('building', $room->building) }}" 
                             placeholder="Contoh: Gedung A, Gedung Rektorat, dll" required>
                      <small class="text-muted">Masukkan nama gedung secara manual</small>
                  </div>
                  <div class="col-md-6">
                      <label for="floor" class="form-label">Lantai</label>
                      <input type="number" class="form-control" name="floor" id="floor"
                             value="{{ old('floor', $room->floor) }}" 
                             min="1" required>
                  </div>
              </div>

              <div class="row mb-3">
                  <label for="name" class="col-sm-2 col-form-label">Nama Ruangan</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="name" id="name"
                             value="{{ old('name', $room->name) }}" required>
                  </div>
              </div>

              <div class="row mb-3">
                  <label for="capacity" class="col-sm-2 col-form-label">Kapasitas</label>
                  <div class="col-sm-10">
                      <div class="input-group">
                          <input type="number" class="form-control" name="capacity" id="capacity"
                                 value="{{ old('capacity', $room->capacity) }}" min="1" required>
                          <span class="input-group-text">orang</span>
                      </div>
                  </div>
              </div>

              <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Fasilitas</label>
                  <div class="col-sm-10">
                      <div class="row">
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="AC" 
                                      {{ $room->hasFacility('AC') ? 'checked' : '' }}>
                                  <label class="form-check-label">AC</label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="Proyektor"
                                      {{ $room->hasFacility('Proyektor') ? 'checked' : '' }}>
                                  <label class="form-check-label">Proyektor</label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="LCD"
                                      {{ $room->hasFacility('LCD') ? 'checked' : '' }}>
                                  <label class="form-check-label">LCD</label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="Papan Tulis"
                                      {{ $room->hasFacility('Papan Tulis') ? 'checked' : '' }}>
                                  <label class="form-check-label">Papan Tulis</label>
                              </div>
                          </div>
                      </div>
                      <div class="row mt-2">
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="WiFi"
                                      {{ $room->hasFacility('WiFi') ? 'checked' : '' }}>
                                  <label class="form-check-label">WiFi</label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="Sound System"
                                      {{ $room->hasFacility('Sound System') ? 'checked' : '' }}>
                                  <label class="form-check-label">Sound System</label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="facilities[]" value="Meja Kursi"
                                      {{ $room->hasFacility('Meja Kursi') ? 'checked' : '' }}>
                                  <label class="form-check-label">Meja Kursi</label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Foto Saat Ini</label>
                  <div class="col-sm-10">
                      @if($room->photo_url)
                          <img src="{{ asset('storage/' . $room->photo_url) }}" 
                               alt="Current Photo" 
                               class="img-thumbnail" 
                               style="max-width: 300px; max-height: 200px;">
                      @else
                          <div class="alert alert-warning">
                              <i class="bi bi-exclamation-triangle"></i> Belum ada foto
                          </div>
                      @endif
                  </div>
              </div>

              <div class="row mb-3">
                  <label for="photo_url" class="col-sm-2 col-form-label">Upload Foto Baru</label>
                  <div class="col-sm-10">
                      <input class="form-control" type="file" name="photo_url" id="photo_url"
                             accept="image/jpeg,image/jpg,image/png">
                      <small class="text-muted">
                          Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, JPEG, PNG. Maksimal 2MB
                      </small>
                  </div>
              </div>
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-cloud-upload"></i> Update ke Firebase
                    </button>
                    <a href="{{ route('admin.display_room') }}" class="btn btn-secondary">
                      <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="reset" class="btn btn-outline-secondary">
                      <i class="bi bi-arrow-clockwise"></i> Reset Form
                    </button>
                  </div>
                </div>
              </div>
            </form><!-- End General Form Elements -->
          </div>
        </div>
      </div>
    </div>
  </section>
</main><!-- End #main -->

<script>
// Preview foto baru sebelum upload
document.getElementById('photo_url').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Cek ukuran file
        if (file.size > 2048 * 1024) { // 2MB
            alert('Ukuran file terlalu besar. Maksimal 2MB');
            e.target.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update preview jika ada
            const currentImg = document.querySelector('img.img-thumbnail');
            if (currentImg) {
                currentImg.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Auto-capitalize input
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