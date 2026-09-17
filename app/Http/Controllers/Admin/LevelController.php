<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

use App\Http\Requests\Admin\StoreLevelRequest;
use App\Http\Requests\Admin\UpdateLevelRequest;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy  = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        try {
            $query = Level::query();

            $levels = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Admin/Level/Index', [
                'levels' => $levels,
                'filters'  => $request->only(['search', 'name', 'description']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Level/Index', [
                'levels' => null,
                'filters'  => $request->only(['search', 'name', 'description']),
                'error'    => 'No se pudieron cargar los niveles. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Level/Create');
    }

    public function store(StoreLevelRequest $request)
    {
        try {
            Level::create($request->validated());

            return redirect()->route('admin.level.index')
                             ->with('message', 'Level created successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo crear el nivel. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        return Inertia::render('Admin/Level/Show', [
            'level' => $level,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        return Inertia::render('Admin/Level/Edit', [
            'level' => $level,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelRequest $request, Level $level)
    {
        try {
            $level->update($request->validated());

            return redirect()->route('admin.level.index')
                             ->with('message', 'Level updated successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar el nivel. Intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        try {
            $level->delete();

            return redirect()->route('admin.level.index')
                             ->with('message', 'Level deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.level.index')
                             ->with('error', 'No se pudo eliminar el nivel. Intenta de nuevo.');
        }
    }
}
