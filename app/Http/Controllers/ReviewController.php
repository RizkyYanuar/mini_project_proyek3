<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class ReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:review-report')];
    }

    public function storeNote(Request $request, Report $report)
    {
        $data = $request->validate([
            'note' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $report->notes()->create([
            'id_user' => $request->user()->id_user,
            'note' => $data['note'],
        ]);

        return back()->with('success', 'Catatan peninjauan ditambahkan.');
    }

    public function updateStatus(Request $request, Report $report, ReportWorkflow $workflow)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Report::STATUSES)],
        ]);

        $workflow->review($report, $data['status']);

        return back()->with('success', 'Status laporan diperbarui.');
    }
}