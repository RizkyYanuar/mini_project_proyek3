<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportForwarding extends Model
{
    public const RECIPIENT_TYPES = ['sekolah', 'sppg', 'faskes', 'lainnya'];

    protected $primaryKey = 'id_forwarding';

    protected $fillable = ['id_report', 'id_user', 'recipient_type', 'recipient_name', 'note', 'forwarded_at'];

    protected function casts(): array
    {
        return ['forwarded_at' => 'datetime'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_report', 'id_report');
    }

    public function forwardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}