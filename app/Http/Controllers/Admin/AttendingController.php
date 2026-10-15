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
                ->with('substituteTeacher')
                ->selectRaw('MIN(id) as id, session_id, id_group, id_teacher, substitute_teacher_id, class_date, MIN(created_at) as created_at')
                ->groupBy('session_id', 'id_group', 'id_teacher', 'substitute_teacher_id', 'class_date');

            $this->applyTeacherVisibility($query);

            $attendings = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            // Fetch extra info for frontend filters
            $teachers = Teacher::pluck('name', 'id')->toArray();
            $groups = $this->canManageAllAttendances()
                ? Group::pluck('name', 'id')->toArray()
                : $this->visibleGroupsForTeacher($this->currentTeacher());

            return Inertia::render('Admin/Attending/Index', [
                'attendings' => $attendings,
                'teachers' => $teachers,
                'groups' => $groups,
                'filters'  => $request->only(['search', 'id_teacher', 'id_group', 'class_date']),
                'can' => [
                    'view' => auth()->user()->can('attending list'),
                    'create' => auth()->user()->can('attending create'),
                    'edit' => auth()->user()->can('attending edit'),
                    'delete' => auth()->user()->can('attending delete'),
                    'export' => auth()->user()->can('attending export'),
                    'export_single' => auth()->user()->can('attending export single'),
                ]
            ]);

        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
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
        $teachers = $this->linkedTeacherOptions();
        $groupQuery = Group::with('students');
        if (! $this->canManageAllAttendances()) {
            $teacher = $this->currentTeacher();
            abort_unless($teacher, 403);
            $groupQuery->where('id_teacher', $teacher->id);
        }
        $groups = $groupQuery->get();

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
            $this->ensureCanCreateForGroup((int) $data['id_group']);
            $id_user = Auth::id(); // Assign the current user who is registering this attendance
            $sessionId = (string) Str::uuid();
            
            foreach ($data['attendances'] as $attendance) {
                Attending::create([
                    'id_user' => $id_user,
                    'session_id' => $sessionId,
                    'id_teacher' => $data['id_teacher'],
                    'substitute_teacher_id' => $data['substitute_teacher_id'] ?? null,
                    'id_group' => $data['id_group'],
                    'class_date' => $data['class_date'],
                    'id_student' => $attendance['id_student'],
                    'status' => $attendance['status'],
                    'social_reason' => $attendance['social_reason'] ?? null,
                ]);
            }

            return redirect()->route('admin.attending.index')
                             ->with('message', 'Attendance created successfully.');

        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
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
        $this->ensureCanAccess($attending);

        $attendances = Attending::with(['student', 'teacher', 'group', 'user'])
            ->where('session_id', $attending->session_id)
            ->get();

        return Inertia::render('Admin/Attending/Show', [
            'attending' => $attending->load(['teacher', 'substituteTeacher', 'group']),
            'group_attendances' => $attendances,
            'can' => [
                'export_single' => auth()->user()->can('attending export single'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attending $attending)
    {
        $this->ensureCanAccess($attending);
        $teachers = $this->linkedTeacherOptions();
        $groupQuery = Group::with('students');
        if (! $this->canManageAllAttendances()) {
            $teacher = $this->currentTeacher();
            abort_unless($teacher, 403);
            $groupQuery->where(function ($query) use ($teacher, $attending) {
                $query->where('id_teacher', $teacher->id)
                    ->orWhere('id', $attending->id_group);
            });
        }
        $groups = $groupQuery->get();

        $attendances = Attending::with('student')
            ->where('session_id', $attending->session_id)
            ->get();

        return Inertia::render('Admin/Attending/Edit', [
            'attending' => $attending->load('substituteTeacher'),
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
            $this->ensureCanAccess($attending);
            $data = $request->validated();
            abort_unless((int) $data['id_group'] === (int) $attending->id_group, 403);
            $id_user = Auth::id();
            $sessionIds = Attending::where('session_id', $attending->session_id)
                ->pluck('id')
                ->flip();

            foreach ($data['attendances'] as $att_data) {
                if (! empty($att_data['id'])) {
                    abort_unless($sessionIds->has((int) $att_data['id']), 403);
                    Attending::where('id', $att_data['id'])
                        ->where('session_id', $attending->session_id)
                        ->update([
                        'id_user' => $id_user,
                        'session_id' => $attending->session_id,
                        'id_teacher' => $data['id_teacher'],
                        'substitute_teacher_id' => $data['substitute_teacher_id'] ?? null,
                        'id_group' => $data['id_group'],
                        'class_date' => $data['class_date'],
                        'status' => $att_data['status'],
                        'social_reason' => $att_data['social_reason'] ?? null,
                    ]);
                } else {
                    Attending::create([
                        'id_user' => $id_user,
                        'session_id' => $attending->session_id,
                        'id_teacher' => $data['id_teacher'],
                        'substitute_teacher_id' => $data['substitute_teacher_id'] ?? null,
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

        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
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
            Attending::where('session_id', $attending->session_id)
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
        $this->ensureCanAccess($attending);

        $group_attendances = Attending::with(['student', 'teacher', 'group', 'user'])
            ->where('session_id', $attending->session_id)
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
            ->selectRaw('MIN(id) as id, session_id, id_group, id_teacher, substitute_teacher_id, class_date, MIN(created_at) as created_at')
            ->groupBy('session_id', 'id_group', 'id_teacher', 'substitute_teacher_id', 'class_date');

        $groupedSessions = $query
            ->filter($request)
            ->orderBy($request->input('sort_by', 'class_date'), $request->input('sort_dir', 'desc'))
            ->get();

        $sessions = [];
        foreach ($groupedSessions as $s) {
            $records = Attending::where('session_id', $s->session_id)
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

    private function canManageAllAttendances(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole(config('admin.roles.super_admin')) || $user->hasRole('administración'));
    }

    private function currentTeacher(): ?Teacher
    {
        return Teacher::where('id_user', Auth::id())->first();
    }

    private function applyTeacherVisibility($query): void
    {
        if ($this->canManageAllAttendances()) {
            return;
        }

        $teacher = $this->currentTeacher();
        if (! $teacher) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where(function ($visible) use ($teacher) {
            $visible->whereHas('group', fn ($group) => $group->where('id_teacher', $teacher->id))
                ->orWhere('substitute_teacher_id', $teacher->id)
                ->orWhere('id_teacher', $teacher->id);
        });
    }

    private function ensureCanCreateForGroup(int $groupId): void
    {
        if ($this->canManageAllAttendances()) {
            return;
        }

        $teacher = $this->currentTeacher();
        abort_unless($teacher && Group::whereKey($groupId)->where('id_teacher', $teacher->id)->exists(), 403);
    }

    private function ensureCanAccess(Attending $attending): void
    {
        if ($this->canManageAllAttendances()) {
            return;
        }

        $teacher = $this->currentTeacher();
        abort_unless($teacher && (
            (int) $attending->group()->value('id_teacher') === (int) $teacher->id
            || (int) $attending->substitute_teacher_id === (int) $teacher->id
            || (int) $attending->id_teacher === (int) $teacher->id
        ), 403);
    }

    private function visibleGroupsForTeacher(?Teacher $teacher): array
    {
        if (! $teacher) {
            return [];
        }

        return Group::where(function ($query) use ($teacher) {
            $query->where('id_teacher', $teacher->id)
                ->orWhereIn('id', Attending::select('id_group')->where('substitute_teacher_id', $teacher->id))
                ->orWhereIn('id', Attending::select('id_group')->where('id_teacher', $teacher->id));
        })->pluck('name', 'id')->toArray();
    }

    private function linkedTeacherOptions(): array
    {
        return Teacher::query()
            ->whereNotNull('id_user')
            ->orderBy('name')
            ->orderBy('last_name')
            ->get(['id', 'name', 'last_name'])
            ->mapWithKeys(fn (Teacher $teacher) => [
                $teacher->id => trim($teacher->name.' '.$teacher->last_name),
            ])
            ->all();
    }
}
