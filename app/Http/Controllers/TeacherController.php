<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

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
            // Como tu tabla no usa SoftDeletes, no necesitamos el match() para 'deleted' o 'all'
            $query = Teacher::query();

            // 3. Encadenar: filtros de búsqueda → ordenamiento → paginación
            $teachers = $query
                ->filter($request)          // scopeFilter del Model
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();        // conserva todos los ?params en los links

            // 4. Pasar datos + estado de UI a Inertia
            return Inertia::render('Teachers/Index', [
                'teachers' => $teachers,
                'sort_by'  => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                // Puedes añadir el filtro de english_level u otros si los mantienes en el estado de Vue
                'filters'  => $request->only(['search', 'name', 'last_name', 'identity_id', 'english_level'])
            ]);

        } catch (Throwable $e) {
            // 5. Fallback seguro: la página se renderiza aunque falle la query
            report($e);
            
            return Inertia::render('Teachers/Index', [
                'teachers' => null,
                'sort_by'  => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                'filters'  => $request->only(['search', 'name', 'last_name', 'identity_id', 'english_level']),
                'error'    => 'No se pudieron cargar los profesores. Intenta de nuevo.',
            ]);
        }
    }
}
