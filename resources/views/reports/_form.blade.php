<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="id_school">Sekolah</label>
        <select name="id_school" id="id_school" class="form-select @error('id_school') is-invalid @enderror" required>
            <option value="">-- Pilih sekolah --</option>
            @foreach ($schools as $school)
                <option value="{{ $school->id_school }}" @selected(old('id_school', $report->id_school) == $school->id_school)>{{ $school->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="id_distribution">Distribusi makanan</label>
        <select name="id_distribution" id="id_distribution"
                data-selected="{{ old('id_distribution', $report->id_distribution) }}"
                class="form-select @error('id_distribution') is-invalid @enderror" required>
            <option value="">-- Pilih sekolah dulu --</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="incident_at">Waktu kejadian</label>
        <input type="datetime-local" name="incident_at" id="incident_at"
               value="{{ old('incident_at', optional($report->incident_at)->format('Y-m-d\TH:i')) }}"
               class="form-control @error('incident_at') is-invalid @enderror" required>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="affected_count">Perkiraan jumlah siswa terdampak (opsional)</label>
        <input type="number" min="0" name="affected_count" id="affected_count"
               value="{{ old('affected_count', $report->affected_count) }}"
               class="form-control @error('affected_count') is-invalid @enderror">
    </div>

    <div class="col-12">
        <label class="form-label" for="title">Judul laporan</label>
        <input type="text" name="title" id="title" maxlength="150" value="{{ old('title', $report->title) }}"
               class="form-control @error('title') is-invalid @enderror" required>
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Deskripsi kejadian</label>
        <textarea name="description" id="description" rows="5"
                  class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $report->description) }}</textarea>
        <div class="form-text">Ceritakan apa yang terjadi. Jangan mencantumkan identitas siswa.</div>
    </div>

    <div class="col-12">
        <label class="form-label" for="evidence">Bukti pendukung (JPG/PNG/PDF, maks. 2 MB)</label>
        <input type="file" name="evidence" id="evidence" accept=".jpg,.jpeg,.png,.pdf"
               class="form-control @error('evidence') is-invalid @enderror">
        @if ($report->evidence_path)
            <div class="form-text">
                File saat ini: <a href="{{ $report->evidence_url }}" target="_blank">{{ $report->evidence_original_name }}</a>
                (unggah file baru untuk menggantinya)
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const schoolSelect = document.getElementById('id_school');
    const distSelect   = document.getElementById('id_distribution');

    async function loadDistributions(schoolId, selected = null) {
        if (!schoolId) {
            distSelect.innerHTML = '<option value="">-- Pilih sekolah dulu --</option>';
            return;
        }
        distSelect.innerHTML = '<option value="">Memuat...</option>';
        try {
            const res = await fetch(`{{ url('/schools') }}/${schoolId}/distributions`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const items = await res.json();

            distSelect.innerHTML = '';
            const first = document.createElement('option');
            first.value = '';
            first.textContent = items.length ? '-- Pilih distribusi --' : 'Belum ada distribusi';
            distSelect.appendChild(first);

            items.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.label;
                if (String(d.id) === String(selected)) opt.selected = true;
                distSelect.appendChild(opt);
            });
        } catch (e) {
            distSelect.innerHTML = '<option value="">Gagal memuat distribusi</option>';
        }
    }

    schoolSelect.addEventListener('change', () => loadDistributions(schoolSelect.value));

    // Saat halaman dibuka ulang (edit / validasi gagal), muat ulang dan pilih distribusi sebelumnya
    if (schoolSelect.value) loadDistributions(schoolSelect.value, distSelect.dataset.selected);
</script>
@endpush