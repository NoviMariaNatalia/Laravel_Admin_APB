@extends('layouts.main_admin')
@section('title', 'Login Admin')
@section('content')
<main>
  <div class="container">
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
            
            <div class="d-flex justify-content-center py-4">
              <a href="{{ route('admin.login') }}" class="logo d-flex align-items-center w-auto">
                <img src="{{ asset('assets/img/logo.png') }}" alt="">
                <span class="d-none d-lg-block">N-Space</span>
              </a>
            </div>

            <!-- {{-- Demo Credentials Info --}}
            <div class="alert alert-info text-center mb-3">
              <h6><i class="bi bi-info-circle"></i> Demo Login</h6>
              <small>
                <strong>Username:</strong> admin<br>
                <strong>Password:</strong> admin123
              </small>
            </div> -->
            
            <div class="card mb-3">
              <div class="card-body">
                <div class="pt-4 pb-2 text-center">
                  <h5 class="card-title pb-0 fs-4">Selamat Datang Admin</h5>
                  <p class="small">Masukkan username & password untuk login</p>
                </div>

                {{-- Alert Messages --}}
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

                @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('admin.login.submit') }}" class="row g-3 needs-validation" novalidate>
                  @csrf
                  
                  <div class="col-12">
                    <label for="yourUsername" class="form-label">Username</label>
                    <div class="input-group has-validation">
                      <span class="input-group-text" id="inputGroupPrepend">
                        <i class="bi bi-person"></i>
                      </span>
                      <input type="text" 
                             name="username" 
                             class="form-control @error('username') is-invalid @enderror" 
                             id="yourUsername" 
                             value="{{ old('username') }}"
                             placeholder="Masukkan username"
                             required>
                      <div class="invalid-feedback">
                        @error('username')
                          {{ $message }}
                        @else
                          Silakan masukkan username.
                        @enderror
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-12">
                    <label for="yourPassword" class="form-label">Password</label>
                    <div class="input-group has-validation">
                      <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                      </span>
                      <input type="password" 
                             name="password" 
                             class="form-control @error('password') is-invalid @enderror" 
                             id="yourPassword"
                             placeholder="Masukkan password"
                             required>
                      <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                      </button>
                      <div class="invalid-feedback">
                        @error('password')
                          {{ $message }}
                        @else
                          Silakan masukkan password.
                        @enderror
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" 
                             type="checkbox" 
                             name="remember" 
                             value="true" 
                             id="rememberMe"
                             {{ old('remember') ? 'checked' : '' }}>
                      <label class="form-check-label" for="rememberMe">
                        Ingat saya (30 hari)
                      </label>
                    </div>
                  </div>
                  
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">
                      <i class="bi bi-box-arrow-in-right"></i> Login Admin
                    </button>
                  </div>

                  <!-- {{-- Quick Login Button --}}
                  <div class="col-12">
                    <button type="button" class="btn btn-outline-info w-100" id="quickLogin">
                      <i class="bi bi-lightning"></i> Quick Login (Demo)
                    </button>
                  </div> -->
                  
                </form>
                
              </div>
            </div>

            {{-- Footer Info --}}
            <div class="text-center">
              <small class="text-muted">
                <i class="bi bi-shield-check"></i> 
                Sistem Login Admin N-Space
              </small>
            </div>
            
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('yourPassword');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        password.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Quick login untuk demo
document.getElementById('quickLogin').addEventListener('click', function() {
    document.getElementById('yourUsername').value = 'admin';
    document.getElementById('yourPassword').value = 'admin123';
    document.getElementById('rememberMe').checked = true;
    
    // Auto submit setelah 1 detik
    setTimeout(function() {
        document.querySelector('form').submit();
    }, 1000);
});

// Auto-focus username
document.getElementById('yourUsername').focus();

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
@endsection