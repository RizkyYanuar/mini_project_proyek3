@extends('layouts.app')
@section('title', 'Data Sekolah')

@section('content')
<h1 class="h4 mb-3">Data Sekolah</h1>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('schools.store') }}" class="row g-2">
            @csrf
            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Nama sekolah" required></div>
            <div class="col-md-2"><input type="text" name="npsn" class="form-control" placeholder="NPSN"></div>
            <div class="col-md-3"><input type="text" name="address" class="form-control" placeholder="Alamat"></div>
            <div class="col-md-2"><input type="text" name="district" class="form-control" placeholder="Kecamatan"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
        </form>
    </div>
</div>

<form method="GET" action="{{ route('schools.index') }}" class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama sekolah..."></div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-primary w-100">Cari</button>
        <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Nama</th><th>NPSN</th><th>Kecamatan</th><th>Distribusi</th><th>Laporan</th><th></th></tr></thead>
            <tbody>
            @forelse ($schools as $school)
                <tr>
                    <td>{{ $school->name }}</td>
                    <td>{{ $school->npsn ?? '-' }}</td>
                    <td>{{ $school->district ?? '-' }}</td>
                    <td>{{ $school->distributions_count }}</td>
                    <td>{{ $school->reports_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('schools.destroy', $school) }}" class="d-inline" onsubmit="return confirm('Hapus sekolah ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada sekolah.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $schools->links() }}</div>
@endsection