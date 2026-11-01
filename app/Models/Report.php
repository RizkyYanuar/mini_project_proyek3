<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\ReportObserver::class])]
class Report extends Model
{
    public const STATUS_DIKIRIM = 'dikirim';
    public const STATUS_DITINJAU = 'ditinjau';
    public const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';
    public const STATUS_PERLU_DITERUSKAN = 'perlu_diteruskan';
    public const STATUS_TELAH_DITERUSKAN = 'telah_diteruskan';
    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_DIKIRIM,
        self::STATUS_DITINJAU,
        self::STATUS_PERLU_PERBAIKAN,
        self::STATUS_PERLU_DITERUSKAN,
        self::STATUS_TELAH_DITERUSKAN,
        self::STATUS_SELESAI,
    ];

    public const EDITABLE_STATUSES = [self::STATUS_DIKIRIM, self::STATUS_PERLU_PERBAIKAN];

    protected $primaryKey = 'id_report';

    protected $fillable = [
        'id_school', 'id_distribution', 'incident_at', 'title', 'description',
        'affected_count', 'evidence_path', 'evidence_original_name', 'evidence_mime',
    ];

    protected function casts(): array
    {
        return ['incident_at' => 'datetime'];
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->evidence_path ? Storage::url($this->evidence_path) : null;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES, true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'id_school', 'id_school');
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(MealDistribution::class, 'id_distribution', 'id_distribution');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ReportNote::class, 'id_report', 'id_report');
    }

    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class, 'id_report', 'id_report');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class, 'id_report', 'id_report');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
            }))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['id_school'] ?? null, fn ($q, $s) => $q->where('id_school', $s));
    }
}