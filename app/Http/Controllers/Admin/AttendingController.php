<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attending;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

use App\Http\Requests\Admin\StoreAttendingRequest;
use App\Http\Requests\Admin\UpdateAttendingRequest;

class AttendingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy  = $request->input('sort_by', 'class_date');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        try {
            $query = Attending::with(['teacher', 'student', 'group', 'user']);

            $attendings = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Admin/Attending/Index', [
                'attendings' => $attendings,
                'filters'  => $request->only(['search', 'id_teacher', 'id_student', 'id_group', 'status']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Attending/Index', [
                'attendings' => null,
                'filters'  => $request->only(['search']),
                'error'    => 'No se pudieron cargar las asistencias. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::pluck('name', 'id')->toArray();
        $students = Student::pluck('name', 'id')->toArray();
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Attending/Create', [
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'statuses' => [
                'asistente' => 'Asistente',
                'inasistente' => 'Inasistente',
                'justificado' => 'Justificado'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendingRequest $request)
    {
        try {
            $data = $request->validated();
            $data['id_user'] = Auth::id(); // Assign the current user who is registering this attendance
            
            Attending::create($data);

            return redirect()->route('admin.attending.index')
                             ->with('message', 'Attendance created successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo registrar la asistencia. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attending $attending)
    {
        $attending->load(['teacher', 'student', 'group', 'user']);

        return Inertia::render('Admin/Attending/Show', [
            'attending' => $attending,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attending $attending)
    {
        $teachers = Teacher::pluck('name', 'id')->toArray();
        $students = Student::pluck('name', 'id')->toArray();
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Attending/Edit', [
            'attending' => $attending,
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'statuses' => [
                'asistente' => 'Asistente',
                'inasistente' => 'Inasistente',
                'justificado' => 'Justificado'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendingRequest $request, Attending $attending)
    {
        try {
            $attending->update($request->validated());

            return redirect()->route('admin.attending.index')
                             ->with('message', 'Attendance updated successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar la asistencia. Intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attending $attending)
    {
        try {
            $attending->delete();

            return redirect()->route('admin.attending.index')
                             ->with('message', 'Attendance deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.attending.index')
                             ->with('error', 'No se pudo eliminar la asistencia. Intenta de nuevo.');
        }
    }
}
