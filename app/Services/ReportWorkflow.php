<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportForwarding;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportWorkflow
{
    public const REVIEW_TRANSITIONS = [
        Report::STATUS_DIKIRIM          => [Report::STATUS_DITINJAU],
        Report::STATUS_DITINJAU         => [
            Report::STATUS_PERLU_PERBAIKAN,
            Report::STATUS_PERLU_DITERUSKAN,
            Report::STATUS_SELESAI,
        ],
        Report::STATUS_TELAH_DITERUSKAN => [Report::STATUS_SELESAI],
    ];

    public function reviewTargets(string $from): array
    {
        return self::REVIEW_TRANSITIONS[$from] ?? [];
    }

    public function review(Report $report, string $to): Report
    {
        return DB::transaction(function () use ($report, $to) {
            $report = Report::lockForUpdate()->findOrFail($report->getKey());

            if (! in_array($to, $this->reviewTargets($report->status), true)) {
                throw ValidationException::withMessages([
                    'status' => "Perubahan status dari '{$report->status}' ke '{$to}' tidak diperbolehkan.",
                ]);
            }

            $report->status = $to;
            $report->save();

            return $report;
        });
    }

    public function forward(Report $report, int $userId, array $data): Report
    {
        return DB::transaction(function () use ($report, $userId, $data) {
            $report = Report::lockForUpdate()->findOrFail($report->getKey());

            if ($report->status !== Report::STATUS_PERLU_DITERUSKAN) {
                throw ValidationException::withMessages([
                    'recipient_type' => 'Penerusan hanya dapat dicatat untuk laporan berstatus "perlu diteruskan".',
                ]);
            }

            ReportForwarding::create([
                'id_report'      => $report->id_report,
                'id_user'        => $userId,
                'recipient_type' => $data['recipient_type'],
                'recipient_name' => $data['recipient_name'],
                'note'           => $data['note'] ?? null,
                'forwarded_at'   => now(),
            ]);

            $report->status = Report::STATUS_TELAH_DITERUSKAN;
            $report->save();

            return $report;
        });
    }

    public function hasActiveDuplicate(int $userId, int $distributionId, ?int $exceptReportId = null): bool
    {
        return Report::where('id_user', $userId)
            ->where('id_distribution', $distributionId)
            ->where('status', '!=', Report::STATUS_SELESAI)
            ->when($exceptReportId, fn ($q, $id) => $q->where('id_report', '!=', $id))
            ->exists();
    }
}