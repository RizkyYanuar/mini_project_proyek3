<?php

namespace App\Http\Controllers;

use App\Models\MealDistribution;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class MealDistributionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:manage-master-data', except: ['bySchool'])];
    }

    public function bySchool(School $school)
    {
        $items = $school->distributions()
            ->whereDate('distribution_date', '<=', today())
            ->orderByDesc('distribution_date')
            ->get(['id_distribution', 'distribution_date', 'menu_description'])
            ->map(fn($d) => [
                'id' => $d->id_distribution,
                'label' => $d->distribution_date->format('d M Y') . ' - ' . $d->menu_description,
            ]);

        return response()->json($items);
    }

    public function index(Request $request)
    {
        $filters = $request->validate(['id_school' => ['nullable', 'integer', 'exists:schools,id_school']]);

        $distributions = MealDistribution::with('school')->withCount('reports')
            ->when($filters['id_school'] ?? null, fn($q, $id) => $q->where('id_school', $id))
            ->orderByDesc('distribution_date')
            ->paginate(10)
            ->withQueryString();

        return view('distributions.index', [
            'distributions' => $distributions,
            'schools' => School::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        MealDistribution::create($this->validated($request));

        return back()->with('success', 'Distribusi ditambahkan.');
    }

    public function edit(MealDistribution $distribution)
    {
        return view('distributions.edit', [
            'distribution' => $distribution,
            'schools' => School::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MealDistribution $distribution)
    {
        $distribution->update($this->validated($request, $distribution));

        return redirect()->route('distributions.index')->with('success', 'Distribusi diperbarui.');
    }

    public function destroy(MealDistribution $distribution)
    {
        if ($distribution->reports()->exists()) {
            return back()->withErrors(['distribution' => 'Distribusi tidak dapat dihapus karena sudah dirujuk oleh laporan.']);
        }

        $distribution->delete();

        return back()->with('success', 'Distribusi dihapus.');
    }

    private function validated(Request $request, ?MealDistribution $distribution = null): array
    {
        return $request->validate([
            'id_school' => ['required', 'integer', 'exists:schools,id_school'],
            'distribution_date' => [
                'required',
                'date',
                Rule::unique('meal_distributions', 'distribution_date')
                    ->where('id_school', $request->input('id_school'))
                    ->ignore($distribution?->id_distribution, 'id_distribution'),
            ],
            'menu_description' => ['required', 'string', 'max:255'],
            'portion_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);
    }
}
