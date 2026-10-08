<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoryMovement;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Admin\StoreHistoryMovementRequest;
use App\Http\Requests\Admin\UpdateHistoryMovementRequest;

class HistoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        try {
            $query = HistoryMovement::with(['student', 'group', 'newGroup', 'user']);

            $movements = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            $groups = Group::pluck('name', 'id')->toArray();

            return Inertia::render('Admin/HistoryMovement/Index', [
                'movements' => $movements,
                'groups' => $groups,
                'filters'  => $request->only(['search', 'id_group', 'id_new_group', 'migrated']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/HistoryMovement/Index', [
                'movements' => null,
                'groups' => Group::pluck('name', 'id')->toArray(),
                'filters'  => $request->only(['search']),
                'error'    => 'No se pudieron cargar los movimientos. Intenta de nuevo.',
            ]);
        }
    }

    public function create()
    {
        $groups = Group::with('students')->get();

        return Inertia::render('Admin/HistoryMovement/Create', [
            'groups' => $groups,
        ]);
    }

    public function store(StoreHistoryMovementRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $id_user = Auth::id();
            $isMigrated = $request->boolean('migrated');
            
            foreach ($data['students'] as $id_student) {
                HistoryMovement::create([
                    'id_student' => $id_student,
                    'id_group' => $data['id_group'],
                    'id_new_group' => $data['id_new_group'],
                    'migrated' => $isMigrated,
                    'id_user' => $id_user,
                ]);

                if ($isMigrated) {
                    Student::where('id', $id_student)->update(['id_group' => $data['id_new_group']]);
                }
            }
            
            DB::commit();

            return redirect()->route('admin.history-movement.index')
                             ->with('message', 'Movimientos de historial creados exitosamente.');

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudieron crear los movimientos. Intenta de nuevo.');
        }
    }

    public function show(HistoryMovement $historyMovement)
    {
        $historyMovement->load(['student', 'group', 'newGroup', 'user']);

        return Inertia::render('Admin/HistoryMovement/Show', [
            'movement' => $historyMovement,
        ]);
    }

    public function edit(HistoryMovement $historyMovement)
    {
        $historyMovement->load(['student', 'group', 'newGroup', 'user']);
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/HistoryMovement/Edit', [
            'movement' => $historyMovement,
            'groups' => $groups,
        ]);
    }

    public function update(UpdateHistoryMovementRequest $request, HistoryMovement $historyMovement)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $isMigrated = $request->boolean('migrated');
            $wasMigrated = $historyMovement->migrated;

            $historyMovement->update([
                'id_group' => $data['id_group'],
                'id_new_group' => $data['id_new_group'],
                'id_student' => $data['id_student'],
                'migrated' => $isMigrated,
                'id_user' => Auth::id(),
            ]);

            // Si antes no estaba migrado y ahora sí, actualizamos el estudiante
            if (!$wasMigrated && $isMigrated) {
                Student::where('id', $data['id_student'])->update(['id_group' => $data['id_new_group']]);
            }

            // Opcional: Si antes estaba migrado y ahora se des-aprueba, ¿lo regresamos?
            if ($wasMigrated && !$isMigrated) {
                 Student::where('id', $data['id_student'])->update(['id_group' => $data['id_group']]);
            }

            DB::commit();

            return redirect()->route('admin.history-movement.index')
                             ->with('message', 'Movimiento actualizado exitosamente.');

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar el movimiento. Intenta de nuevo.');
        }
    }

    public function destroy(HistoryMovement $historyMovement)
    {
        try {
            $historyMovement->delete();

            return redirect()->route('admin.history-movement.index')
                             ->with('message', 'Movimiento eliminado exitosamente.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.history-movement.index')
                             ->with('error', 'No se pudo eliminar el movimiento. Intenta de nuevo.');
        }
    }
}
