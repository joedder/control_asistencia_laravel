<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Models\User;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Leer parámetros de la URL con defaults seguros
        $sortBy  = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        try {
            // 2. Definir la consulta base
            $query = Teacher::query();

            // 3. Encadenar: filtros de búsqueda → ordenamiento → paginación
            $teachers = $query
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            // 4. Pasar datos a Inertia
            return Inertia::render('Admin/Teacher/Index', [
                'teachers' => $teachers,
                'filters'  => $request->only(['search', 'name', 'last_name', 'identity_id', 'english_level']),
                'can' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                ]
            ]);

        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Teacher/Index', [
                'teachers' => null,
                'filters'  => $request->only(['search', 'name', 'last_name', 'identity_id', 'english_level']),
                'error'    => 'No se pudieron cargar los profesores. Intenta de nuevo.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener usuarios que NO tienen un registro en teachers
        $users = User::whereNotIn('id', function($query) {
            $query->select('id_user')->from('teachers')->whereNotNull('id_user');
        })->pluck('name', 'id')->toArray();

        return Inertia::render('Admin/Teacher/Create', [
            'users' => $users,
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            // El request ya viene validado gracias a StoreTeacherRequest
            Teacher::create($request->validated());

            // Retornar a la lista con un mensaje flash de éxito
            return redirect()->route('admin.teacher.index')
                             ->with('message', 'Teacher created successfully.');

        } catch (Throwable $e) {
            // Fallback seguro: capturamos el error y retornamos a la vista con el mensaje
            report($e);
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'No se pudo crear el profesor. Intenta de nuevo.');
        }
    }
}
