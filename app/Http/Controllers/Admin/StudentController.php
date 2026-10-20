<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;

class StudentController extends Controller
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
            $query = Student::with(['group']);

            $students = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            $groups = Group::pluck('name', 'id')->toArray();

            return Inertia::render('Admin/Student/Index', [
                'students' => $students,
                'groups' => $groups,
                'filters'  => $request->only(['search', 'name', 'last_name', 'identity_id', 'id_group']),
                'can' => [
                    'view' => auth()->user()->can('student list'),
                    'create' => auth()->user()->can('student create'),
                    'edit' => auth()->user()->can('student edit'),
                    'delete' => auth()->user()->can('student delete'),
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Student/Index', [
                'students' => null,
                'filters'  => $request->only(['search']),
                'error'    => 'No se pudieron cargar los estudiantes. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Student/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            Student::create($request->validated());

            return redirect()->route('admin.student.index')
                             ->with('message', 'Student created successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo crear el estudiante. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['group']);

        return Inertia::render('Admin/Student/Show', [
            'student' => $student,
        ]);
    }

    /** Show the attendance history and summary for one student. */
    public function attendanceReport(Student $student)
    {
        $report = $this->studentAttendanceData($student);

        return Inertia::render('Admin/Student/AttendanceReport', [
            'student' => $report['student'],
            'attendances' => $report['attendances'],
            'stats' => $report['stats'],
        ]);
    }

    /** Stream a printable PDF of one student's attendance history. */
    public function attendanceReportPdf(Student $student)
    {
        $report = $this->studentAttendanceData($student);
        $filename = 'reporte_asistencia_' . $student->id . '_' . now()->format('Ymd_His') . '.pdf';

        return Pdf::loadView('reports.student-attendance', $report)
            ->setPaper('a4', 'portrait')
            ->stream($filename);
    }

    private function studentAttendanceData(Student $student): array
    {
        $student->load('group');
        $attendances = $student->attendings()
            ->with(['group', 'teacher', 'substituteTeacher'])
            ->orderByDesc('class_date')
            ->orderByDesc('id')
            ->get();

        $total = $attendances->count();
        $presentes = $attendances->where('status', 'asistente')->count();
        $inasistentes = $attendances->where('status', 'inasistente')->count();
        $justificados = $attendances->where('status', 'justificado')->count();

        return [
            'student' => $student,
            'attendances' => $attendances,
            'stats' => [
                'total' => $total,
                'presentes' => $presentes,
                'inasistentes' => $inasistentes,
                'justificados' => $justificados,
                'pct_presentes' => $total > 0 ? round(($presentes / $total) * 100, 1) : 0,
            ],
            'generatedAt' => Carbon::now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $groups = Group::pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Student/Edit', [
            'student' => $student,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            $student->update($request->validated());

            return redirect()->route('admin.student.index')
                             ->with('message', 'Student updated successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo actualizar el estudiante. Intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();

            return redirect()->route('admin.student.index')
                             ->with('message', 'Student deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.student.index')
                             ->with('error', 'No se pudo eliminar el estudiante. Intenta de nuevo.');
        }
    }
}
