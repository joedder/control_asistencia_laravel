<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

use App\Http\Requests\Admin\StoreCategoryGroupRequest;
use App\Http\Requests\Admin\UpdateCategoryGroupRequest;

class CategoryGroupController extends Controller
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
            $query = CategoryGroup::query();

            $categoryGroups = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Admin/CategoryGroup/Index', [
                'category_groups' => $categoryGroups,
                'filters'  => $request->only(['search', 'name', 'description']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/CategoryGroup/Index', [
                'category_groups' => null,
                'filters'  => $request->only(['search', 'name', 'description']),
                'error'    => 'No se pudieron cargar los grupos de categorías. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CategoryGroup/Create');
    }

    public function store(StoreCategoryGroupRequest $request)
    {
        try {
            CategoryGroup::create($request->validated());

            return redirect()->route('admin.category-group.index')
                             ->with('message', 'Category Group created successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo crear el grupo de categorías. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryGroup $categoryGroup)
    {
        return Inertia::render('Admin/CategoryGroup/Show', [
            'category_group' => $categoryGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryGroup $categoryGroup)
    {
        return Inertia::render('Admin/CategoryGroup/Edit', [
            'category_group' => $categoryGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryGroupRequest $request, CategoryGroup $categoryGroup)
    {
        try {
            $categoryGroup->update($request->validated());

            return redirect()->route('admin.category-group.index')
                             ->with('message', 'Category Group updated successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar el grupo de categorías. Intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryGroup $categoryGroup)
    {
        try {
            $categoryGroup->delete();

            return redirect()->route('admin.category-group.index')
                             ->with('message', 'Category Group deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.category-group.index')
                             ->with('error', 'No se pudo eliminar el grupo de categorías. Intenta de nuevo.');
        }
    }
}
