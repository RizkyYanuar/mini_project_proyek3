<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RecapController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:view-recap')];
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'id_school' => ['nullable', 'integer', 'exists:schools,id_school'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $base = Report::query()
            ->when($filters['id_school'] ?? null, fn ($q, $v) => $q->where('id_school', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));

        $summary = [
            'total' => (clone $base)->count(),
            'diproses' => (clone $base)->where('status', '!=', Report::STATUS_SELESAI)->count(),
            'perlu_diteruskan' => (clone $base)->where('status', Report::STATUS_PERLU_DITERUSKAN)->count(),
            'selesai' => (clone $base)->where('status', Report::STATUS_SELESAI)->count(),
        ];

        $rows = (clone $base)
            ->selectRaw("id_school,
                COUNT(*) AS total,
                SUM(status <> 'selesai') AS diproses,
                SUM(status = 'perlu_diteruskan') AS perlu_diteruskan,
                SUM(status = 'selesai') AS selesai")
            ->groupBy('id_school')
            ->with('school')
            ->get()
            ->sortByDesc('total');

        return view('recap.index', [
            'summary' => $summary,
            'rows'    => $rows,
            'schools' => School::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }
}