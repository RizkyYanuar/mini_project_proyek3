@extends('layouts.app')
@section('title', 'Data Distribusi')

@section('content')
<h1 class="h4 mb-3">Data Distribusi Makanan</h1>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('distributions.store') }}" class="row g-2">
            @csrf
            <div class="col-md-3">
                <select name="id_school" class="form-select" required>
                    <option value="">-- Sekolah --</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id_school }}" @selected(old('id_school') == $school->id_school)>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="distribution_date" value="{{ old('distribution_date') }}" class="form-control" required></div>
            <div class="col-md-3"><input type="text" name="menu_description" value="{{ old('menu_description') }}" class="form-control" placeholder="Menu" required></div>
            <div class="col-md-2"><input type="number" min="1" name="portion_count" value="{{ old('portion_count') }}" class="form-control" placeholder="Porsi"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
        </form>
    </div>
</div>

<form method="GET" action="{{ route('distributions.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="id_school" class="form-select">
            <option value="">Semua sekolah</option>
            @foreach ($schools as $school)
                <option value="{{ $school->id_school }}" @selected(($filters['id_school'] ?? '') == $school->id_school)>{{ $school->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-primary w-100">Filter</button>
        <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Tanggal</th><th>Sekolah</th><th>Menu</th><th>Porsi</th><th>Laporan</th><th></th></tr></thead>
            <tbody>
            @forelse ($distributions as $d)
                <tr>
                    <td>{{ $d->distribution_date->format('d M Y') }}</td>
                    <td>{{ $d->school->name }}</td>
                    <td>{{ $d->menu_description }}</td>
                    <td>{{ $d->portion_count ?? '-' }}</td>
                    <td>{{ $d->reports_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('distributions.edit', $d) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('distributions.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Hapus distribusi ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada distribusi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $distributions->links() }}</div>
@endsection