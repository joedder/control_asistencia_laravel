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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
            $query = Attending::with(['teacher', 'group'])
                ->selectRaw('MIN(id) as id, id_group, id_teacher, class_date')
                ->groupBy('id_group', 'id_teacher', 'class_date');

            $attendings = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            // Fetch extra info for frontend filters
            $teachers = Teacher::pluck('name', 'id')->toArray();
            $groups = Group::pluck('name', 'id')->toArray();

            return Inertia::render('Admin/Attending/Index', [
                'attendings' => $attendings,
                'teachers' => $teachers,
                'groups' => $groups,
                'filters'  => $request->only(['search', 'id_teacher', 'id_group', 'class_date']),
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
            // Delete all attendances for the same group and date
            Attending::where('id_group', $attending->id_group)
                ->whereDate('class_date', $attending->class_date)
                ->delete();

            return redirect()->route('admin.attending.index')
                             ->with('message', 'Attendances deleted successfully.');

        } catch (Throwable $e) {
            report($e);
            
            return redirect()->route('admin.attending.index')
                             ->with('error', 'No se pudo eliminar la asistencia. Intenta de nuevo.');
        }
    }

    /**
     * Export a single batch attendance session to PDF.
     */
    public function exportSinglePdf(Request $request, Attending $attending)
    {
        $group_attendances = Attending::with(['student', 'teacher', 'group', 'user'])
            ->where('id_group', $attending->id_group)
            ->whereDate('class_date', $attending->class_date)
            ->get();

        $total = $group_attendances->count();
        $presentes = $group_attendances->where('status', 'asistente')->count();
        $inasistentes = $group_attendances->where('status', 'inasistente')->count();
        $justificados = $group_attendances->where('status', 'justificado')->count();

        $stats = [
            'total' => $total,
            'presentes' => $presentes,
            'inasistentes' => $inasistentes,
            'justificados' => $justificados,
            'pct_presentes' => $total > 0 ? round(($presentes / $total) * 100, 1) : 0,
            'pct_inasistentes' => $total > 0 ? round(($inasistentes / $total) * 100, 1) : 0,
            'pct_justificados' => $total > 0 ? round(($justificados / $total) * 100, 1) : 0,
        ];

        $formattedDate = Carbon::parse($attending->class_date)->format('d/m/Y');
        $fileDate = Carbon::parse($attending->class_date)->format('Y-m-d');
        $groupName = Str::slug($attending->group->name ?? 'grupo');
        $filename = "asistencia_{$groupName}_{$fileDate}.pdf";

        $pdf = Pdf::loadView('reports.attending-single', [
            'attending' => $attending->load(['teacher', 'group', 'user']),
            'group_attendances' => $group_attendances,
            'stats' => $stats,
            'formattedDate' => $formattedDate,
        ])->setPaper('a4', 'portrait');

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Export general / filtered attendances summary report to PDF.
     */
    public function exportReportPdf(Request $request)
    {
        $query = Attending::with(['teacher', 'group'])
            ->selectRaw('MIN(id) as id, id_group, id_teacher, class_date')
            ->groupBy('id_group', 'id_teacher', 'class_date');

        $groupedSessions = $query
            ->filter($request)
            ->orderBy($request->input('sort_by', 'class_date'), $request->input('sort_dir', 'desc'))
            ->get();

        $sessions = [];
        foreach ($groupedSessions as $s) {
            $records = Attending::where('id_group', $s->id_group)
                ->whereDate('class_date', $s->class_date)
                ->get();
            
            $tot = $records->count();
            $pres = $records->where('status', 'asistente')->count();
            $inas = $records->where('status', 'inasistente')->count();

            $sessions[] = [
                'id' => $s->id,
                'class_date' => $s->class_date,
                'group_name' => $s->group->name ?? 'N/A',
                'teacher_name' => ($s->teacher->name ?? 'N/A') . ' ' . ($s->teacher->last_name ?? ''),
                'total' => $tot,
                'presentes' => $pres,
                'inasistentes' => $inas,
                'pct' => $tot > 0 ? round(($pres / $tot) * 100, 1) : 0,
            ];
        }

        $appliedFilters = [];
        if ($request->filled('search')) {
            $appliedFilters[] = "Búsqueda: '{$request->search}'";
        }
        if ($request->filled('id_teacher')) {
            $t = Teacher::find($request->id_teacher);
            if ($t) $appliedFilters[] = "Docente: {$t->name} {$t->last_name}";
        }
        if ($request->filled('id_group')) {
            $g = Group::find($request->id_group);
            if ($g) $appliedFilters[] = "Grupo: {$g->name}";
        }
        if ($request->filled('class_date')) {
            $appliedFilters[] = "Fecha: " . Carbon::parse($request->class_date)->format('d/m/Y');
        }

        $pdf = Pdf::loadView('reports.attending-general', [
            'sessions' => $sessions,
            'appliedFilters' => $appliedFilters,
        ])->setPaper('a4', 'portrait');

        $filename = "reporte_general_asistencias_" . now()->format('Ymd_His') . ".pdf";

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
