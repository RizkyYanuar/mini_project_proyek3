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
use Illuminate\Support\Facades\Storage;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor1    = User::where('email', 'pelapor@mbg.test')->first();
        $pelapor2    = User::where('email', 'pelapor2@mbg.test')->first();
        $pemantau1   = User::where('email', 'pemantau@mbg.test')->first();
        $pemantau2   = User::where('email', 'pemantau2@mbg.test')->first();
        $koordinator = User::where('email', 'koordinator@mbg.test')->first();

        $harapan   = School::where('npsn', '10000001')->first();
        $cicendo    = School::where('npsn', '10000002')->first();
        $kopo = School::where('npsn', '10000003')->first();
        $lebak   = School::where('npsn', '10000004')->first();

        $dist = fn (School $s, int $daysAgo) => MealDistribution::where('id_school', $s->id_school)
            ->whereDate('distribution_date', now()->subDays($daysAgo)->toDateString())
            ->firstOrFail();

        $evidence = function (string $type): array {
            if ($type === 'png') {
                $name = 'evidence/demo-bukti.png';
                Storage::disk('public')->put($name, base64_decode(
                    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
                ));
                return ['evidence_path' => $name, 'evidence_original_name' => 'foto-dummy.png', 'evidence_mime' => 'image/png'];
            }

            $name = 'evidence/demo-bukti.pdf';
            Storage::disk('public')->put($name, "%PDF-1.1\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 100]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF");
            return ['evidence_path' => $name, 'evidence_original_name' => 'dokumen-dummy.pdf', 'evidence_mime' => 'application/pdf'];
        };

        $data = [
            // #1 dikirim (editable) - sekaligus pemicu BR-04 untuk pelapor1 di distribusi ini
            [
                'user' => $pelapor1, 'school' => $harapan, 'dist' => $dist($harapan, 2),
                'title' => 'Siswa mual setelah makan siang',
                'description' => 'Data dummy: beberapa siswa mengeluh mual dan sakit perut beberapa jam setelah makan.',
                'affected_count' => 5, 'evidence' => 'png',
                'history' => ['dikirim'],
            ],
            // #2 ditinjau - distribusi sama dengan #1 (demo keterkaitan laporan)
            [
                'user' => $pelapor2, 'school' => $harapan, 'dist' => $dist($harapan, 2),
                'title' => 'Keluhan sakit perut pada siswa kelas 8',
                'description' => 'Data dummy: laporan tambahan dari orang tua siswa lain pada distribusi yang sama.',
                'affected_count' => 3, 'evidence' => null,
                'history' => ['dikirim', 'ditinjau'],
            ],
            // #3 perlu_perbaikan (editable; setelah diedit kembali ke dikirim)
            [
                'user' => $pelapor1, 'school' => $cicendo, 'dist' => $dist($cicendo, 5),
                'title' => 'Makanan berbau tidak segar',
                'description' => 'Data dummy: laporan perlu dilengkapi dengan informasi waktu kejadian lebih jelas.',
                'affected_count' => null, 'evidence' => null,
                'history' => ['dikirim', 'ditinjau', 'perlu_perbaikan'],
            ],
            // #4 perlu_diteruskan (siap dicatat penerusannya oleh koordinator)
            [
                'user' => $pelapor2, 'school' => $kopo, 'dist' => $dist($kopo, 5),
                'title' => 'Beberapa siswa pusing dan muntah',
                'description' => 'Data dummy: laporan sudah ditinjau dan ditandai perlu diteruskan.',
                'affected_count' => 8, 'evidence' => 'pdf',
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan'],
            ],
            // #5 telah_diteruskan (bisa ditandai selesai oleh pemantau)
            [
                'user' => $pelapor1, 'school' => $kopo, 'dist' => $dist($kopo, 9),
                'title' => 'Siswa diare setelah konsumsi makanan',
                'description' => 'Data dummy: informasi sudah diteruskan ke pihak terkait.',
                'affected_count' => 4, 'evidence' => null,
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan', 'telah_diteruskan'],
            ],
            // #6 selesai
            [
                'user' => $pelapor2, 'school' => $cicendo, 'dist' => $dist($cicendo, 9),
                'title' => 'Keluhan gatal setelah makan',
                'description' => 'Data dummy: proses pemantauan telah selesai.',
                'affected_count' => 2, 'evidence' => null,
                'history' => ['dikirim', 'ditinjau', 'perlu_diteruskan', 'telah_diteruskan', 'selesai'],
            ],
            // #7 ditinjau di sekolah lain (agar rekap memuat 4 sekolah)
            [
                'user' => $pelapor1, 'school' => $lebak, 'dist' => $dist($lebak, 2),
                'title' => 'Siswa mengeluh pusing usai makan',
                'description' => 'Data dummy: laporan sedang ditinjau oleh pemantau komunitas.',
                'affected_count' => 1, 'evidence' => null,
                'history' => ['dikirim', 'ditinjau'],
            ],
        ];

        foreach ($data as $i => $row) {
            $history  = $row['history'];
            $final    = end($history);
            $created  = now()->subDays(8 - $i);
            $pemantau = $i % 2 === 0 ? $pemantau1 : $pemantau2;

            $attrs = [
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
            ];

            if ($row['evidence']) {
                $attrs += $evidence($row['evidence']);
            }

            $report = Report::unguarded(fn () => Report::create($attrs));

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
                $note = $final === 'perlu_perbaikan'
                    ? 'Data dummy: mohon lengkapi waktu kejadian dan jumlah siswa terdampak.'
                    : 'Data dummy: informasi laporan telah diperiksa kelengkapannya.';

                ReportNote::unguarded(fn () => ReportNote::create([
                    'id_report' => $report->id_report,
                    'id_user'   => $pemantau->id_user,
                    'note'      => $note,
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