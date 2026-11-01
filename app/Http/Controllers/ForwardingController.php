<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportForwarding;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class ForwardingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:forward-report')];
    }

    public function store(Request $request, Report $report, ReportWorkflow $workflow)
    {
        $data = $request->validate([
            'recipient_type' => ['required', Rule::in(ReportForwarding::RECIPIENT_TYPES)],
            'recipient_name' => ['required', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $workflow->forward($report, $request->user()->id_user, $data);

        return back()->with('success', 'Penerusan informasi berhasil dicatat.');
    }
}