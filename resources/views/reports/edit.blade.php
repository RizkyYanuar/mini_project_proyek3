@extends('layouts.app')
@section('title', 'Perbaiki Laporan')

@section('content')
<h1 class="h4 mb-3">Perbaiki Laporan</h1>

@if ($report->status === 'perlu_perbaikan')
    <div class="alert alert-warning">Laporan ini diminta untuk dilengkapi. Setelah disimpan, laporan akan dikirim ulang untuk ditinjau.</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('reports._form')
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection