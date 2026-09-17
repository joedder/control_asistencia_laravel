<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\CategoryGroup;
use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

use App\Http\Requests\Admin\StoreGroupRequest;
use App\Http\Requests\Admin\UpdateGroupRequest;

class GroupController extends Controller
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
            $query = Group::with(['teacher', 'categoryGroup', 'level']);

            $groups = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Admin/Group/Index', [
                'groups' => $groups,
                'filters'  => $request->only(['search', 'name', 'id_teacher', 'id_category_group', 'id_level']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Group/Index', [
                'groups' => null,
                'filters'  => $request->only(['search', 'name']),
                'error'    => 'No se pudieron cargar los grupos. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::pluck('name', 'id')->toArray();
        $categoryGroups = CategoryGroup::pluck('name', 'id')->toArray();
        $levels = Level::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Group/Create', [
            'teachers' => $teachers,
            'category_groups' => $categoryGroups,
            'levels' => $levels,
        ]);
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            Group::create($request->validated());

            return redirect()->route('admin.group.index')
                             ->with('message', 'Group created successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo crear el grupo. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group->load(['teacher', 'categoryGroup', 'level']);

        return Inertia::render('Admin/Group/Show', [
            'group' => $group,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $teachers = Teacher::pluck('name', 'id')->toArray();
        $categoryGroups = CategoryGroup::pluck('name', 'id')->toArray();
        $levels = Level::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Group/Edit', [
            'group' => $group,
            'teachers' => $teachers,
            'category_groups' => $categoryGroups,
            'levels' => $levels,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        try {
            $group->update($request->validated());

            return redirect()->route('admin.group.index')
                             ->with('message', 'Group updated successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar el grupo. Intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        try {
            $group->delete();

            return redirect()->route('admin.group.index')
                             ->with('message', 'Group deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.group.index')
                             ->with('error', 'No se pudo eliminar el grupo. Intenta de nuevo.');
        }
    }
}
