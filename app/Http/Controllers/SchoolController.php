<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class SchoolController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:manage-master-data')];
    }

    public function index(Request $request)
    {
        $search = $request->validate(['search' => ['nullable', 'string', 'max:100']])['search'] ?? null;

        $schools = School::withCount(['distributions', 'reports'])
            ->when($search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('schools.index', compact('schools', 'search'));
    }

    public function store(Request $request)
    {
        School::create($this->validated($request));

        return back()->with('success', 'Sekolah ditambahkan.');
    }

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $school->update($this->validated($request, $school));

        return redirect()->route('schools.index')->with('success', 'Sekolah diperbarui.');
    }

    public function destroy(School $school)
    {
        if ($school->distributions()->exists() || $school->reports()->exists()) {
            return back()->withErrors(['school' => 'Sekolah tidak dapat dihapus karena sudah memiliki distribusi atau laporan.']);
        }

        $school->delete();

        return back()->with('success', 'Sekolah dihapus.');
    }

    private function validated(Request $request, ?School $school = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'npsn' => ['nullable', 'string', 'max:20', Rule::unique('schools', 'npsn')->ignore($school?->id_school, 'id_school')],
            'address' => ['nullable', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
        ]);
    }
}