@extends('layouts.app')
@section('title', 'Edit Distribusi')

@section('content')
<h1 class="h4 mb-3">Edit Distribusi</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('distributions.update', $distribution) }}" class="row g-3">
            @csrf @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Sekolah</label>
                <select name="id_school" class="form-select" required>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id_school }}" @selected(old('id_school', $distribution->id_school) == $school->id_school)>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" name="distribution_date" value="{{ old('distribution_date', $distribution->distribution_date->format('Y-m-d')) }}" class="form-control" required></div>
            <div class="col-md-8"><label class="form-label">Menu</label><input type="text" name="menu_description" value="{{ old('menu_description', $distribution->menu_description) }}" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Jumlah porsi</label><input type="number" min="1" name="portion_count" value="{{ old('portion_count', $distribution->portion_count) }}" class="form-control"></div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection