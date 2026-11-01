@props(['status'])
@php
    $color = match ($status) {
        'dikirim'          => 'secondary',
        'ditinjau'         => 'info',
        'perlu_perbaikan'  => 'warning',
        'perlu_diteruskan' => 'danger',
        'telah_diteruskan' => 'primary',
        'selesai'          => 'success',
        default            => 'dark',
    };
@endphp
<span class="badge text-bg-{{ $color }}">{{ str_replace('_', ' ', $status) }}</span>