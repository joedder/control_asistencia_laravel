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
            $query = HistoryMovement::with(['group', 'newGroup', 'user'])
                ->selectRaw('MIN(id) as id, batch_id, MIN(created_at) as created_at, id_group, id_new_group, migrated, id_user, COUNT(id_student) as student_count')
                ->groupBy('batch_id', 'id_group', 'id_new_group', 'migrated', 'id_user');

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
        $groups = Group::with(['students' => function ($query) {
            $query->withExists(['historyMovements as has_pending_migration' => function ($q) {
                $q->where('migrated', false);
            }]);
        }])->get();

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
            $batch_id = (string) \Illuminate\Support\Str::uuid();
            
            foreach ($data['students'] as $id_student) {
                HistoryMovement::create([
                    'batch_id' => $batch_id,
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
        // Load all students in the batch
        $batchMovements = HistoryMovement::with(['student'])
            ->where('batch_id', $historyMovement->batch_id)
            ->get();

        $historyMovement->load(['group', 'newGroup', 'user']);

        return Inertia::render('Admin/HistoryMovement/Show', [
            'movement' => $historyMovement,
            'batchMovements' => $batchMovements,
        ]);
    }

    public function edit(HistoryMovement $historyMovement)
    {
        $batchMovements = HistoryMovement::with(['student'])
            ->where('batch_id', $historyMovement->batch_id)
            ->get();

        $historyMovement->load(['group', 'newGroup', 'user']);
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/HistoryMovement/Edit', [
            'movement' => $historyMovement,
            'batchMovements' => $batchMovements,
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
            $batch_id = $historyMovement->batch_id;

            // Delete removed students
            $studentsToKeep = $data['students'];
            $removedMovements = HistoryMovement::where('batch_id', $batch_id)
                ->whereNotIn('id_student', $studentsToKeep)
                ->get();
            
            foreach ($removedMovements as $rm) {
                // If it was already migrated, revert it
                if ($wasMigrated) {
                    Student::where('id', $rm->id_student)->update(['id_group' => $historyMovement->id_group]);
                }
                $rm->delete();
            }

            // Update remaining students
            HistoryMovement::where('batch_id', $batch_id)->update([
                'id_group' => $data['id_group'],
                'id_new_group' => $data['id_new_group'],
                'migrated' => $isMigrated,
                'id_user' => Auth::id(),
            ]);

            foreach ($studentsToKeep as $id_student) {
                if (!$wasMigrated && $isMigrated) {
                    Student::where('id', $id_student)->update(['id_group' => $data['id_new_group']]);
                }

                if ($wasMigrated && !$isMigrated) {
                     Student::where('id', $id_student)->update(['id_group' => $historyMovement->id_group]); // return to ORIGINAL group
                }
            }

            DB::commit();

            return redirect()->route('admin.history-movement.index')
                             ->with('message', 'Movimientos actualizados exitosamente.');

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
        DB::beginTransaction();
        try {
            $batch_id = $historyMovement->batch_id;
            
            // If it was already migrated, revert before deleting
            if ($historyMovement->migrated) {
                $students = HistoryMovement::where('batch_id', $batch_id)->pluck('id_student');
                Student::whereIn('id', $students)->update(['id_group' => $historyMovement->id_group]);
            }

            HistoryMovement::where('batch_id', $batch_id)->delete();
            DB::commit();

            return redirect()->route('admin.history-movement.index')
                             ->with('message', 'Movimiento eliminado exitosamente.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.history-movement.index')
                             ->with('error', 'No se pudo eliminar el movimiento. Intenta de nuevo.');
        }
    }
}
