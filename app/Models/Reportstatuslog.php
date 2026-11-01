<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatusLog extends Model
{
    protected $primaryKey = 'id_log';

    protected $fillable = ['id_report', 'id_user', 'action', 'old_status', 'new_status'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_report', 'id_report');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}