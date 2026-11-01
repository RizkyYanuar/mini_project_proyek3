@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $report->title }}</h1>
    <div class="d-flex gap-2 align-items-center">
        <x-status-badge :status="$report->status" />
        @can('update-report', $report)
            <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        @endcan
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Sekolah</dt>
                    <dd class="col-sm-8">{{ $report->school->name }}</dd>

                    <dt class="col-sm-4">Distribusi</dt>
                    <dd class="col-sm-8">
                        {{ $report->distribution->distribution_date->format('d M Y') }} - {{ $report->distribution->menu_description }}
                    </dd>

                    <dt class="col-sm-4">Waktu kejadian</dt>
                    <dd class="col-sm-8">{{ $report->incident_time->format('d M Y H:i') }}</dd>

                    <dt class="col-sm-4">Perkiraan terdampak</dt>
                    <dd class="col-sm-8">{{ $report->affected_count ?? '-' }}</dd>

                    @unless (auth()->user()->isPelapor())
                        <dt class="col-sm-4">Pelapor</dt>
                        <dd class="col-sm-8">{{ $report->user->name }}</dd>
                    @endunless

                    <dt class="col-sm-4">Deskripsi</dt>
                    <dd class="col-sm-8">{!! nl2br(e($report->description)) !!}</dd>

                    <dt class="col-sm-4">Bukti pendukung</dt>
                    <dd class="col-sm-8">
                        @if ($report->evidence_path)
                            <a href="{{ $report->evidence_url }}" target="_blank">{{ $report->evidence_original_name }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header">Catatan peninjauan</div>
            <ul class="list-group list-group-flush">
                @forelse ($report->notes as $note)
                    <li class="list-group-item">
                        <div>{{ $note->note }}</div>
                        <small class="text-muted">{{ $note->author->name }} &middot; {{ $note->created_at->format('d M Y H:i') }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada catatan.</li>
                @endforelse
            </ul>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header">Penerusan informasi kepada pihak terkait</div>
            <ul class="list-group list-group-flush">
                @forelse ($report->forwardings as $f)
                    <li class="list-group-item">
                        <strong>{{ strtoupper($f->recipient_type) }}</strong> &mdash; {{ $f->recipient_name }}
                        @if ($f->note)<div>{{ $f->note }}</div>@endif
                        <small class="text-muted">dicatat oleh {{ $f->forwardedBy->name }} &middot; {{ $f->forwarded_at->format('d M Y H:i') }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada penerusan yang dicatat.</li>
                @endforelse
            </ul>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Perkembangan status</div>
            <ul class="list-group list-group-flush">
                @forelse ($report->statusLogs->sortBy('created_at') as $log)
                    <li class="list-group-item small">
                        {{ $log->old_status ? str_replace('_', ' ', $log->old_status) : '(baru)' }}
                        &rarr; <strong>{{ str_replace('_', ' ', $log->new_status) }}</strong>
                        <span class="text-muted">&middot; {{ $log->created_at->format('d M Y H:i') }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada perubahan status.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Pemantau Komunitas --}}
        @can('review-report')
            <div class="card shadow-sm mb-3">
                <div class="card-header">Tinjau laporan</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.notes.store', $report) }}" class="mb-3">
                        @csrf
                        <label class="form-label" for="note">Catatan</label>
                        <textarea name="note" id="note" rows="3" class="form-control mb-2" required></textarea>
                        <button class="btn btn-sm btn-primary">Simpan catatan</button>
                    </form>

                    @if ($reviewTargets)
                        <hr>
                        <div class="small text-muted mb-2">Ubah status:</div>
                        <div class="d-grid gap-2">
                            @foreach ($reviewTargets as $target)
                                <form method="POST" action="{{ route('reports.review', $report) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $target }}">
                                    <button class="btn btn-sm btn-outline-secondary w-100">{{ ucfirst(str_replace('_', ' ', $target)) }}</button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endcan

        {{-- Koordinator Komunitas --}}
        @can('forward-report')
            @if ($report->status === 'perlu_diteruskan')
                <div class="card shadow-sm mb-3">
                    <div class="card-header">Catat penerusan informasi</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.forward', $report) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label" for="recipient_type">Pihak terkait</label>
                                <select name="recipient_type" id="recipient_type" class="form-select" required>
                                    <option value="sekolah">Sekolah</option>
                                    <option value="sppg">SPPG</option>
                                    <option value="faskes">Fasilitas kesehatan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="recipient_name">Nama pihak</label>
                                <input type="text" name="recipient_name" id="recipient_name" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="forward_note">Catatan (opsional)</label>
                                <textarea name="note" id="forward_note" rows="2" class="form-control"></textarea>
                            </div>
                            <button class="btn btn-sm btn-primary">Catat penerusan</button>
                        </form>
                    </div>
                </div>
            @endif
        @endcan

        {{-- Keterkaitan laporan (bukan untuk pelapor) --}}
        @unless (auth()->user()->isPelapor())
            <div class="card shadow-sm">
                <div class="card-header">Laporan lain pada distribusi yang sama</div>
                <ul class="list-group list-group-flush">
                    @forelse ($related as $r)
                        <li class="list-group-item">
                            <a href="{{ route('reports.show', $r) }}">{{ $r->title }}</a>
                            <div class="small"><x-status-badge :status="$r->status" /> <span class="text-muted">{{ $r->user->name }}</span></div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Tidak ada.</li>
                    @endforelse
                </ul>
            </div>
        @endunless
    </div>
</div>
@endsection