@extends('layouts.app')
@section('title', 'Daftar Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ auth()->user()->isPelapor() ? 'Laporan Saya' : 'Semua Laporan' }}</h1>
    @can('create-report')
        <a href="{{ route('reports.create') }}" class="btn btn-primary">Buat Laporan</a>
    @endcan
</div>

<form method="GET" action="{{ route('reports.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Cari judul/deskripsi...">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ str_replace('_', ' ', $s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="id_school" class="form-select">
            <option value="">Semua sekolah</option>
            @foreach ($schools as $school)
                <option value="{{ $school->id_school }}" @selected(($filters['id_school'] ?? '') == $school->id_school)>{{ $school->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-primary w-100">Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Sekolah</th>
                    <th>Tgl Distribusi</th>
                    @unless (auth()->user()->isPelapor())
                        <th>Pelapor</th>
                    @endunless
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->school->name }}</td>
                    <td>{{ $report->distribution->distribution_date->format('d M Y') }}</td>
                    @unless (auth()->user()->isPelapor())
                        <td>{{ $report->user->name }}</td>
                    @endunless
                    <td><x-status-badge :status="$report->status" /></td>
                    <td>{{ $report->created_at->format('d M Y') }}</td>
                    <td class="text-end"><a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada laporan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $reports->links() }}</div>
@endsection