@extends('layouts.main_admin')

@section('title', 'Edit Ruang')

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
                    <li class="breadcrumb-item active">Data Ruang</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="bi bi-cloud-check text-success"></i> 
                    Data Ruang dari Firebase Firestore
                </h5>
                <span class="badge bg-info">{{ count($rooms) }} ruang tersimpan</span>
            </div>

            @if(count($rooms) > 0)
                <div class="container">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($rooms as $room)
                        <div class="col">
                            <div class="card text-center h-100 shadow-sm">
                                @if($room->photo_url)
                                    <img src="{{ asset('storage/' . $room->photo_url) }}" 
                                         class="card-img-top" 
                                         alt="{{ $room->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $room->name }}</h5>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-building"></i> {{ $room->building }} - Lantai {{ $room->floor }}
                                        </small>
                                    </div>
                                    <p class="card-text">
                                        <i class="bi bi-people"></i> Kapasitas: {{ $room->capacity }} orang
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-gear"></i> 
                                        <strong>Fasilitas:</strong><br>
                                        <small>{{ $room->getFacilitiesString() }}</small>
                                    </p>
                                    
                                    <!-- Firebase Info -->
                                    <div class="mt-auto">
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-cloud"></i> ID: {{ $room->id }}
                                        </small>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.edit_room', $room->id) }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.delete_room', $room->id) }}" 
                                                  method="POST" class="flex-fill">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm w-100" 
                                                        onclick="return confirmDelete('{{ $room->name }}')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Belum ada data ruang</h5>
                        <p class="text-muted">Mulai tambahkan data ruang melalui menu Input Data Ruang</p>
                        <a href="{{ route('admin.create_room') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Ruang Pertama
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </main><!-- End #main -->
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
    function confirmDelete(roomName) {
        return confirm(`Apakah Anda yakin ingin menghapus ruangan "${roomName}"?\n\nData ini akan dihapus dari Firebase dan tidak dapat dikembalikan.`);
    }

    // Auto refresh page setiap 30 detik untuk sync dengan Firebase
    // setTimeout(function() {
    //     location.reload();
    // }, 30000);
    </script>
@endpush