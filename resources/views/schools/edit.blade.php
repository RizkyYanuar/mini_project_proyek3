@extends('layouts.app')
@section('title', 'Edit Sekolah')

@section('content')
<h1 class="h4 mb-3">Edit Sekolah</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('schools.update', $school) }}" class="row g-3">
            @csrf @method('PUT')
            <div class="col-md-6"><label class="form-label">Nama</label><input type="text" name="name" value="{{ old('name', $school->name) }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">NPSN</label><input type="text" name="npsn" value="{{ old('npsn', $school->npsn) }}" class="form-control"></div>
            <div class="col-md-8"><label class="form-label">Alamat</label><input type="text" name="address" value="{{ old('address', $school->address) }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Kecamatan</label><input type="text" name="district" value="{{ old('district', $school->district) }}" class="form-control"></div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection