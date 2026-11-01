@extends('layouts.app')
@section('title', 'Rekap Laporan')

@section('content')
<h1 class="h4 mb-3">Rekap Laporan</h1>

<form method="GET" action="{{ route('recap') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="id_school" class="form-select">
            <option value="">Semua sekolah</option>
            @foreach ($schools as $school)
                <option value="{{ $school->id_school }}" @selected(($filters['id_school'] ?? '') == $school->id_school)>{{ $school->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control" title="Dari tanggal"></div>
    <div class="col-md-3"><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control" title="Sampai tanggal"></div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-primary w-100">Terapkan</button>
        <a href="{{ route('recap') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="row g-3 mb-4">
    @foreach ([
        'total' => 'Total laporan',
        'diproses' => 'Masih diproses',
        'perlu_diteruskan' => 'Perlu diteruskan',
        'selesai' => 'Selesai',
    ] as $key => $label)
        <div class="col-6 col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="display-6">{{ $summary[$key] }}</div>
                    <div class="text-muted small">{{ $label }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm">
    <div class="card-header">Laporan per sekolah</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Sekolah</th><th class="text-end">Total</th><th class="text-end">Diproses</th><th class="text-end">Perlu diteruskan</th><th class="text-end">Selesai</th></tr>
            </thead>
            <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->school->name }}</td>
                    <td class="text-end">{{ $row->total }}</td>
                    <td class="text-end">{{ $row->diproses }}</td>
                    <td class="text-end">{{ $row->perlu_diteruskan }}</td>
                    <td class="text-end">{{ $row->selesai }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection