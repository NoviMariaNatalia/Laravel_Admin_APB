@extends('layouts.main_admin')

@section('title', 'Index Admin')

@section('content')
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.index_admin') }}">
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
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index_admin') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Request -->
                        <div class="col-12">
                            <div class="card request overflow-auto">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="#">Today</a></li>
                                        <li><a class="dropdown-item" href="#">This Month</a></li>
                                        <li><a class="dropdown-item" href="#">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Pengajuan Terbaru <span>| Today</span></h5>
                                    
                                    {{-- Error handling untuk Firebase --}}
                                    @if(isset($error))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <strong>Error:</strong> {{ $error }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    {{-- Loading indicator --}}
                                    <div id="loading" class="text-center" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p>Memuat data booking...</p>
                                    </div>

                                    <table class="table table-hover">
                                        <thead class="text-center">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nama Pemesan</th>
                                                <th scope="col">No. HP</th>
                                                <th scope="col">Ruangan</th>
                                                <th scope="col">Tanggal Mulai</th>
                                                <th scope="col">Tanggal Selesai</th>
                                                <th scope="col">Jam Mulai</th>
                                                <th scope="col">Jam Selesai</th>
                                                <th scope="col">Tujuan</th>
                                                <th scope="col">Organisasi</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Check if bookings exist and handle Firebase data --}}
                                            @if(isset($bookings) && count($bookings) > 0)
                                                @foreach($bookings as $booking)
                                                <tr id="booking-row-{{ $booking->id }}">
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    {{-- Firebase field: nama (bukan nama_pemesan) --}}
                                                    <td>{{ $booking->nama ?? '-' }}</td>
                                                    {{-- Firebase field: noHp (bukan no_hp) --}}
                                                    <td>{{ $booking->noHp ?? '-' }}</td>
                                                    {{-- Firebase field: ruangan (langsung nama ruangan, bukan ID) --}}
                                                    <td>{{ $booking->ruangan ?? '-' }}</td>
                                                    {{-- Firebase field: tanggalMulai --}}
                                                    <td>
                                                        {{ $booking->tanggalMulai ?? '-' }}
                                                    </td>
                                                    {{-- Firebase field: tanggalSelesai --}}
                                                    <td>
                                                        {{ $booking->tanggalSelesai ?? '-' }}
                                                    </td>
                                                    {{-- Firebase field: jamMulai --}}
                                                    <td>
                                                        {{ $booking->jamMulai ?? '-' }}
                                                    </td>
                                                    {{-- Firebase field: jamSelesai --}}
                                                    <td>
                                                        {{ $booking->jamSelesai ?? '-' }}
                                                    </td>
                                                    <td>{{ $booking->tujuan ?? '-' }}</td>
                                                    <td>{{ $booking->organisasi ?? '-' }}</td>
                                                    {{-- Status dengan badge --}}
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $booking->status === 'approved' ? 'success' : ($booking->status === 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ $booking->status === 'approved' ? 'Disetujui' : ($booking->status === 'rejected' ? 'Ditolak' : 'Pending') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{-- Hanya tampilkan tombol jika status pending --}}
                                                        @if($booking->status === 'pending')
                                                            <button type="button" 
                                                                    class="btn btn-success btn-sm rounded-pill me-1" 
                                                                    onclick="updateStatus('{{ $booking->id }}', 'Disetujui')"
                                                                    id="approve-btn-{{ $booking->id }}">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-danger btn-sm rounded-pill" 
                                                                    onclick="updateStatus('{{ $booking->id }}', 'Ditolak')"
                                                                    id="reject-btn-{{ $booking->id }}">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">Sudah diproses</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="12" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="bi bi-inbox fs-2"></i>
                                                            <p class="mt-2">Tidak ada pengajuan booking pending saat ini</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- End Recent Sales -->
                    </div>
                </div><!-- End Left side columns -->
            </div>
        </section>
    </main><!-- End #main -->

    <script>
        function updateStatus(bookingId, status) {
            console.log('🔵 Starting updateStatus:', { bookingId, status }); // Debug
            
            if (confirm('Apakah Anda yakin ingin ' + (status === 'Disetujui' ? 'menyetujui' : 'menolak') + ' pengajuan ini?')) {
                
                // Check CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                console.log('🔍 CSRF Token element:', csrfToken); // Debug
                
                if (!csrfToken) {
                    alert('❌ CSRF token tidak ditemukan! Pastikan ada meta tag csrf-token di <head>');
                    return;
                }
                
                const tokenValue = csrfToken.getAttribute('content');
                console.log('🔑 CSRF Token value:', tokenValue); // Debug
                
                // Show loading state
                const approveBtn = document.getElementById(`approve-btn-${bookingId}`);
                const rejectBtn = document.getElementById(`reject-btn-${bookingId}`);
                
                if (approveBtn) approveBtn.disabled = true;
                if (rejectBtn) rejectBtn.disabled = true;
                
                // Construct URL
                const url = `/admin/bookings/${bookingId}/update-status`;
                console.log('🌐 Request URL:', url); // Debug
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenValue,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => {
                    console.log('📡 Response status:', response.status); // Debug
                    console.log('📡 Response headers:', response.headers); // Debug
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Response data:', data); // Debug
                    
                    if (data.success) {
                        alert('✅ Status berhasil diperbarui');
                        
                        // Reload page untuk update data
                        window.location.reload();
                    } else {
                        alert('❌ Gagal memperbarui status: ' + (data.error || data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                })
                .finally(() => {
                    // Re-enable buttons
                    if (approveBtn) approveBtn.disabled = false;
                    if (rejectBtn) rejectBtn.disabled = false;
                });
            }
        }
    </script>


@endsection