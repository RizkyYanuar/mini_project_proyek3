<?php

namespace Database\Seeders;

use App\Models\MealDistribution;
use App\Models\Report;
use App\Models\ReportForwarding;
use App\Models\ReportNote;
use App\Models\ReportStatusLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor1 = User::where('email', 'pelapor@mbg.test')->first();
        $pelapor2 = User::where('email', 'pelapor2@mbg.test')->first();
        $pemantau = User::where('email', 'pemantau@mbg.test')->first();
        $koordinator = User::where('email', 'koordinator@mbg.test')->first();

        $harapan = School::where('npsn', '10000001')->first();
        $cicendo = School::where('npsn', '10000002')->first();
        $kopo = School::where('npsn', '10000003')->first();

        $dist = fn (School $s, int $daysAgo) => MealDistribution::where('id_school', $s->id_school)
            ->whereDate('distribution_date', now()->subDays($daysAgo)->toDateString())
            ->firstOrFail();

        $data = [
            [
                'user' => $pelapor1, 'school' => $harapan, 'dist' => $dist($harapan, 2),
                'title' => 'Siswa mual setelah makan siang',
                'description' => 'Data dummy: beberapa siswa mengeluh mual dan sakit perut beberapa jam setelah makan.',
                'affected_count' => 5,
                'history' => ['dikirim'],
            ],
            [
                'user' => $pelapor2, 'school' => $harapan, 'dist' => $dist($harapan, 2),
                'title' => 'Keluhan sakit perut pada siswa kelas 8',
                'description' => 'Data dummy: laporan tambahan dari orang tua siswa lain pada distribusi yang sama.',
                'affected_count' => 3,
                'history' => ['dikirim', 'ditinjau'],
            ],
            [
                'user' => $pelapor1, 'school' => $cicendo, 'dist' => $dist($cicendo, 5),
                'title' => 'Makanan berbau tidak segar',
                'description' => 'Data dummy: laporan perlu dilengkapi dengan informasi waktu kejadian lebih jelas.',
                'affected_count' => null,
                'history' => ['dikirim', 'ditinjau', 'perlu_perbaikan'],
            ],
            [
                'user' => $pelapor2, 'school' => $kopo, 'dist' => $dist($kopo, 5),
                'title' => 'Beberapa siswa pusing dan muntah',
                'description' => 'Data dummy: laporan sudah ditinjau dan ditandai perlu diteruskan.',
                'affected_count' => 8,
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan'],
            ],
            [
                'user' => $pelapor1, 'school' => $kopo, 'dist' => $dist($kopo, 9),
                'title' => 'Siswa diare setelah konsumsi makanan',
                'description' => 'Data dummy: informasi sudah diteruskan ke pihak terkait.',
                'affected_count' => 4,
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan', 'telah_diteruskan'],
            ],
            [
                'user' => $pelapor2, 'school' => $cicendo, 'dist' => $dist($cicendo, 9),
                'title' => 'Keluhan gatal setelah makan',
                'description' => 'Data dummy: proses pemantauan telah selesai.',
                'affected_count' => 2,
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan', 'telah_diteruskan', 'selesai'],
            ],
        ];

        foreach ($data as $i => $row) {
            $history = $row['history'];
            $final   = end($history);
            $created = now()->subDays(8 - $i);

            $report = Report::unguarded(fn () => Report::create([
                'id_user'         => $row['user']->id_user,
                'id_school'       => $row['school']->id_school,
                'id_distribution' => $row['dist']->id_distribution,
                'incident_time'     => $row['dist']->distribution_date->copy()->setTime(13, 0),
                'title'           => $row['title'],
                'description'     => $row['description'],
                'affected_count'  => $row['affected_count'],
                'status'          => $final,
                'created_at'      => $created,
                'updated_at'      => $created,
            ]));

            foreach ($history as $k => $status) {
                ReportStatusLog::unguarded(fn () => ReportStatusLog::create([
                    'id_report'  => $report->id_report,
                    'id_user'    => $k === 0 ? $row['user']->id_user : $pemantau->id_user,
                    'action'     => 'report.status.changed',
                    'old_status' => $k === 0 ? null : $history[$k - 1],
                    'new_status' => $status,
                ]));
            }

            if (count($history) > 1) {
                ReportNote::unguarded(fn () => ReportNote::create([
                    'id_report' => $report->id_report,
                    'id_user'   => $pemantau->id_user,
                    'note'      => 'Data dummy: informasi laporan telah diperiksa kelengkapannya.',
                ]));
            }

            if (in_array($final, ['telah_diteruskan', 'selesai'], true)) {
                ReportForwarding::unguarded(fn () => ReportForwarding::create([
                    'id_report'      => $report->id_report,
                    'id_user'        => $koordinator->id_user,
                    'recipient_type' => 'faskes',
                    'recipient_name' => 'Puskesmas Contoh (dummy)',
                    'note'           => 'Informasi laporan telah diteruskan.',
                    'forwarded_at'   => now()->subDays(1),
                ]));
            }
        }
    }
}