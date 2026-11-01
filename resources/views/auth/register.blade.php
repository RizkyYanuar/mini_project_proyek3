@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Daftar sebagai Pelapor</h1>
                <form method="POST" action="{{ route('register.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="255"
                               class="form-control @error('name') is-invalid @enderror" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255"
                               class="form-control @error('email') is-invalid @enderror" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" minlength="8"
                               class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                               class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Daftar</button>
                </form>
                <div class="text-center mt-3 small">
                    Sudah punya akun? <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection