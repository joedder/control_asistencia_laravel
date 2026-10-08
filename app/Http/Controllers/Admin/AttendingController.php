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
        $groups = Group::with('students')->get();

        return Inertia::render('Admin/Attending/Create', [
            'teachers' => $teachers,
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
            $id_user = Auth::id(); // Assign the current user who is registering this attendance
            
            foreach ($data['attendances'] as $attendance) {
                Attending::create([
                    'id_user' => $id_user,
                    'id_teacher' => $data['id_teacher'],
                    'id_group' => $data['id_group'],
                    'class_date' => $data['class_date'],
                    'id_student' => $attendance['id_student'],
                    'status' => $attendance['status'],
                    'social_reason' => $attendance['social_reason'] ?? null,
                ]);
            }

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
        // Get all attendances for the same group and class_date
        $attendances = Attending::with(['student', 'teacher', 'group', 'user'])
            ->where('id_group', $attending->id_group)
            ->whereDate('class_date', $attending->class_date)
            ->get();

        return Inertia::render('Admin/Attending/Show', [
            'attending' => $attending->load(['teacher', 'group']),
            'group_attendances' => $attendances,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attending $attending)
    {
        $teachers = Teacher::pluck('name', 'id')->toArray();
        $groups = Group::with('students')->get();

        $attendances = Attending::with('student')
            ->where('id_group', $attending->id_group)
            ->whereDate('class_date', $attending->class_date)
            ->get();

        return Inertia::render('Admin/Attending/Edit', [
            'attending' => $attending,
            'group_attendances' => $attendances,
            'teachers' => $teachers,
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
            $data = $request->validated();
            $id_user = Auth::id();

            foreach ($data['attendances'] as $att_data) {
                if (isset($att_data['id']) && $att_data['id']) {
                    Attending::where('id', $att_data['id'])->update([
                        'id_user' => $id_user,
                        'id_teacher' => $data['id_teacher'],
                        'id_group' => $data['id_group'],
                        'class_date' => $data['class_date'],
                        'status' => $att_data['status'],
                        'social_reason' => $att_data['social_reason'] ?? null,
                    ]);
                } else {
                    Attending::create([
                        'id_user' => $id_user,
                        'id_teacher' => $data['id_teacher'],
                        'id_group' => $data['id_group'],
                        'class_date' => $data['class_date'],
                        'id_student' => $att_data['id_student'],
                        'status' => $att_data['status'],
                        'social_reason' => $att_data['social_reason'] ?? null,
                    ]);
                }
            }

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
