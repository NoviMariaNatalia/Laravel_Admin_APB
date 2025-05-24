@extends('layouts.main_admin')

@section('title', 'Riwayat Admin')

@push('styles')
<link href="{{ asset('assets/css/style_Admin.css') }}" rel="stylesheet">
@endpush

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
                <a class="nav-link collapsed" href="{{ route('admin.display_room') }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Edit Data Ruang</span>
                </a>
            </li><!-- End Edit Data Nav -->

            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.bookings.history') }}">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span>Riwayat</span>
                </a>
            </li><!-- End Riwayat Nav -->
        </ul>
    </aside><!-- End Sidebar -->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Riwayat Booking</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index_admin') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">Riwayat</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">DATA RIWAYAT PENGAJUAN RUANGAN</h5>
                            
                            {{-- Error handling untuk Firebase --}}
                            @if(isset($error))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ $error }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Summary stats --}}
                            @if(isset($bookings) && count($bookings) > 0)
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <h5 class="card-title text-success">Disetujui</h5>
                                                <h3 class="text-success">
                                                    {{ collect($bookings)->where('status', 'approved')->count() }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-danger">
                                            <div class="card-body text-center">
                                                <h5 class="card-title text-danger">Ditolak</h5>
                                                <h3 class="text-danger">
                                                    {{ collect($bookings)->where('status', 'rejected')->count() }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <h5 class="card-title text-primary">Total</h5>
                                                <h3 class="text-primary">{{ count($bookings) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Filter status (optional) --}}
                            <div class="mb-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary active" onclick="filterStatus('all')">
                                        Semua
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="filterStatus('approved')">
                                        Disetujui
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="filterStatus('rejected')">
                                        Ditolak
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="historyTable">
                                    <thead class="text-center align-middle table-light">
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
                                            <th scope="col">Tanggal Pengajuan</th>
                                            <th scope="col">Tanggal Diproses</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle">
                                        {{-- Check if bookings exist and handle Firebase data --}}
                                        @if(isset($bookings) && count($bookings) > 0)
                                            @foreach($bookings as $booking)
                                            <tr class="booking-row" data-status="{{ $booking->status }}">
                                                <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                                {{-- Firebase field: nama (bukan nama_pemesan) --}}
                                                <td>{{ $booking->nama ?? '-' }}</td>
                                                {{-- Firebase field: noHp (bukan no_hp) --}}
                                                <td>{{ $booking->noHp ?? '-' }}</td>
                                                {{-- Firebase field: ruangan (langsung nama ruangan) --}}
                                                <td class="text-center">{{ $booking->ruangan ?? '-' }}</td>
                                                {{-- Firebase field: tanggalMulai --}}
                                                <td class="text-center">
                                                    {{ $booking->tanggalMulai ?? '-' }}
                                                </td>
                                                {{-- Firebase field: tanggalSelesai --}}
                                                <td class="text-center">
                                                    {{ $booking->tanggalSelesai ?? '-' }}
                                                </td>
                                                {{-- Firebase field: jamMulai --}}
                                                <td class="text-center">
                                                    {{ $booking->jamMulai ?? '-' }}
                                                </td>
                                                {{-- Firebase field: jamSelesai --}}
                                                <td class="text-center">
                                                    {{ $booking->jamSelesai ?? '-' }}
                                                </td>
                                                <td>{{ $booking->tujuan ?? '-' }}</td>
                                                <td class="text-center">{{ $booking->organisasi ?? '-' }}</td>
                                                {{-- Firebase field: createdAt --}}
                                                <td class="text-center">
                                                    @if($booking->createdAt)
                                                        {{ $booking->createdAt->format('d-m-Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                {{-- Firebase field: updatedAt --}}
                                                <td class="text-center">
                                                    @if($booking->updatedAt)
                                                        {{ \Carbon\Carbon::parse($booking->updatedAt)->format('d-m-Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                {{-- Status dengan badge dan icon --}}
                                                <td class="text-center">
                                                    @if($booking->status === 'approved')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> Disetujui
                                                        </span>
                                                    @elseif($booking->status === 'rejected')
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle"></i> Ditolak
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock"></i> {{ ucfirst($booking->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="13" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="bi bi-archive fs-1"></i>
                                                        <h5 class="mt-3">Belum ada riwayat booking</h5>
                                                        <p>Riwayat booking yang sudah diproses akan muncul di sini</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination info (jika diperlukan) --}}
                            @if(isset($bookings) && count($bookings) > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        Menampilkan {{ count($bookings) }} data riwayat booking
                                    </small>
                                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh Data
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    // Filter function untuk status
    function filterStatus(status) {
        console.log('🔍 Filter clicked:', status); // Debug
        
        const rows = document.querySelectorAll('.booking-row');
        const buttons = document.querySelectorAll('.btn-group button');
        
        console.log('📊 Found rows:', rows.length); // Debug
        console.log('🔘 Found buttons:', buttons.length); // Debug
        
        // Reset button states
        buttons.forEach(btn => btn.classList.remove('active'));
        
        // Set active button
        event.target.classList.add('active');
        
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const rowStatus = row.getAttribute('data-status');
            console.log(`📋 Row ${index}: status="${rowStatus}"`); // Debug
            
            if (status === 'all') {
                row.style.display = '';
                visibleCount++;
            } else {
                if (rowStatus === status) {
                    row.style.display = '';
                    visibleCount++;
                    console.log(`✅ Row ${index}: SHOWING (${rowStatus} === ${status})`); // Debug
                } else {
                    row.style.display = 'none';
                    console.log(`❌ Row ${index}: HIDING (${rowStatus} !== ${status})`); // Debug
                }
            }
        });
        
        console.log(`👁️ Visible count: ${visibleCount}`); // Debug
        
        // Update counter
        updateCounter();
    }

    function updateCounter() {
        const visibleRows = document.querySelectorAll('.booking-row:not([style*="display: none"])');
        const counter = document.querySelector('.text-muted small');
        
        console.log('📊 Counter update - visible rows:', visibleRows.length); // Debug
        
        if (counter) {
            counter.textContent = `Menampilkan ${visibleRows.length} data riwayat booking`;
        }
    }

    // Auto-run saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Page loaded, initializing counter'); // Debug
        updateCounter();
    });
    </script>

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
@endpush
@endsection