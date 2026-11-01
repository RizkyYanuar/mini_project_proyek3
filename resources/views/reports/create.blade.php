@extends('layouts.app')
@section('title', 'Buat Laporan')

@section('content')
<h1 class="h4 mb-3">Buat Laporan</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf
            @include('reports._form')
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary">Kirim Laporan</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection