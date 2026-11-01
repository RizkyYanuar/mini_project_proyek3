<?php

namespace App\Http\Controllers;

use App\Models\MealDistribution;
use App\Models\Report;
use App\Models\School;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller implements HasMiddleware
{
    public function __construct(private ReportWorkflow $workflow) {}

    public static function middleware(): array
    {
        return [new Middleware('can:create-report', only: ['create', 'store'])];
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Report::STATUSES)],
            'id_school' => ['nullable', 'integer', 'exists:schools,id_school'],
        ]);

        $user = $request->user();

        $reports = Report::with(['school', 'distribution', 'user'])
            ->when($user->isPelapor(), fn ($q) => $q->where('id_user', $user->id_user))
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', [
            'reports' => $reports,
            'schools' => School::orderBy('name')->get(),
            'statuses' => Report::STATUSES,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('reports.create', [
            'report' => new Report(),
            'schools' => School::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->assertBusinessRules($request->user()->id_user, $data);

        $report = $request->user()->reports()->make(Arr::except($data, ['evidence']));
        $report->status = Report::STATUS_DIKIRIM;

        if ($file = $request->file('evidence')) {
            $report->evidence_path = $file->store('evidence', 'public');
            $report->evidence_original_name = $file->getClientOriginalName();
            $report->evidence_mime = $file->getMimeType();
        }

        $report->save();

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Request $request, Report $report)
    {
        Gate::authorize('view-report', $report);

        $report->load(['school', 'distribution', 'user', 'notes.author', 'forwardings.forwardedBy', 'statusLogs.actor']);

        $related = $request->user()->isPelapor()
            ? collect()
            : Report::with('user')
                ->where('id_distribution', $report->id_distribution)
                ->whereKeyNot($report->getKey())
                ->get();

        return view('reports.show', [
            'report' => $report,
            'related' => $related,
            'reviewTargets' => $this->workflow->reviewTargets($report->status),
        ]);
    }

    public function edit(Report $report)
    {
        Gate::authorize('update-report', $report);

        return view('reports.edit', [
            'report' => $report,
            'schools' => School::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Report $report)
    {
        Gate::authorize('update-report', $report);

        $data = $this->validated($request);
        $this->assertBusinessRules($request->user()->id_user, $data, $report);

        $report->fill(Arr::except($data, ['evidence']));

        if ($file = $request->file('evidence')) {
            if ($report->evidence_path) {
                Storage::disk('public')->delete($report->evidence_path);
            }
            $report->evidence_path = $file->store('evidence', 'public');
            $report->evidence_original_name = $file->getClientOriginalName();
            $report->evidence_mime = $file->getMimeType();
        }

        if ($report->status === Report::STATUS_PERLU_PERBAIKAN) {
            $report->status = Report::STATUS_DIKIRIM;
        }

        $report->save();

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'id_school' => ['required', 'integer', 'exists:schools,id_school'],
            'id_distribution' => [
                'required', 'integer',
                Rule::exists('meal_distributions', 'id_distribution')
                    ->where('id_school', $request->input('id_school')),
            ],
            'incident_at' => ['required', 'date', 'before_or_equal:now'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'affected_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);
    }

    private function assertBusinessRules(int $userId, array $data, ?Report $current = null): void
    {
        $distribution = MealDistribution::findOrFail($data['id_distribution']);

        if (Carbon::parse($data['incident_at'])->lt($distribution->distribution_date->copy()->startOfDay())) {
            throw ValidationException::withMessages([
                'incident_at' => 'Waktu kejadian tidak boleh sebelum tanggal distribusi.',
            ]);
        }

        if ($this->workflow->hasActiveDuplicate($userId, (int) $data['id_distribution'], $current?->id_report)) {
            throw ValidationException::withMessages([
                'id_distribution' => 'Anda sudah memiliki laporan yang masih diproses untuk distribusi ini.',
            ]);
        }
    }
}