<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportNote extends Model
{
    protected $primaryKey = 'id_note';

    protected $fillable = ['id_report', 'id_user', 'note'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_report', 'id_report');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}