<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\ReportStatusLog;
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    public function updated(Report $report): void
    {
        if (! $report->wasChanged('status')) return;

        ReportStatusLog::create([
            'id_report'  => $report->id_report,
            'id_user'    => Auth::id(),
            'action'     => 'report.status.changed',
            'old_status' => $report->getRawOriginal('status'),
            'new_status' => $report->status,
        ]);
    }
}