# Guía de Creación de Módulos (Laravel + Vue Admin)

Esta guía documenta el paso a paso exacto para crear un nuevo módulo (CRUD completo) estandarizado bajo la arquitectura de **Laravel, Inertia.js y la plantilla administrativa BalajiDharma/Vue-Admin**.

A modo de ejemplo, usaremos el nombre genérico **`Entity`** (reemplázalo por tu modelo real, ej: `Student`, `Course`, etc.).

---

## 1. Base de Datos y Modelo

### 1.1. Crear Migración y Modelo
Abre tu terminal y ejecuta:
```bash
php artisan make:model Entity -m
```

### 1.2. Definir la Migración
Abre el archivo generado en `database/migrations/` y define tus columnas:
```php
public function up()
{
    Schema::create('entities', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        // ... otras columnas ...
        $table->timestamps();
    });
}
```
Luego ejecuta `php artisan migrate`.

### 1.3. Configurar el Modelo (`app/Models/Entity.php`)
Debes agregar los campos `$fillable` y el método `scopeFilter` estándar para la búsqueda:
```php
class Entity extends Model
{
    use HasFactory;

    protected $fillable = ['name' /*, ... */];

    // Scope para el buscador del Index.vue
    public function scopeFilter($query, $request)
    {
        $query
            ->when($request->filled('name'), fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn($sub) =>
                    $sub->where('name', 'like', '%' . $request->search . '%')
                );
            });

        return $query;
    }
}
```

---

## 2. Validaciones (Form Requests)

Crea los archivos de validación para separar la lógica de reglas del controlador.
```bash
php artisan make:request Admin/StoreEntityRequest
php artisan make:request Admin/UpdateEntityRequest
```

En `StoreEntityRequest`:
```php
public function rules()
{
    return [
        'name' => ['required', 'string', 'max:100'],
        // Si hay campos únicos: 'identity' => ['required', 'unique:entities,identity']
    ];
}
```

En `UpdateEntityRequest` (para ignorar la regla unique del propio registro):
```php
public function rules()
{
    $entity = $this->route('entity'); // Obtener el ID de la URL
    return [
        'name' => ['required', 'string', 'max:100'],
        // 'identity' => ['required', Rule::unique('entities', 'identity')->ignore($entity->id ?? null)],
    ];
}
```

---

## 3. Controlador

Crea el controlador dentro de la carpeta `Admin`:
```bash
php artisan make:controller Admin/EntityController --resource
```

Tu controlador debe lucir así:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Http\Requests\Admin\StoreEntityRequest;
use App\Http\Requests\Admin\UpdateEntityRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        $sortBy  = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        try {
            $entities = Entity::query()
                ->filter($request)
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Admin/Entity/Index', [
                'entities' => $entities,
                'filters'  => $request->only(['search', 'name']),
                'can' => ['create' => true, 'edit' => true, 'delete' => true]
            ]);
        } catch (Throwable $e) {
            report($e);
            return Inertia::render('Admin/Entity/Index', [
                'entities' => null,
                'filters'  => $request->only(['search', 'name']),
                'error'    => 'Error loading data.',
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Admin/Entity/Create');
    }

    public function store(StoreEntityRequest $request)
    {
        try {
            Entity::create($request->validated());
            return redirect()->route('admin.entity.index')->with('message', 'Created successfully.');
        } catch (Throwable $e) {
            report($e);
            return redirect()->back()->withInput()->with('error', 'Creation failed.');
        }
    }

    public function show(Entity $entity)
    {
        return Inertia::render('Admin/Entity/Show', ['entity' => $entity]);
    }

    public function edit(Entity $entity)
    {
        return Inertia::render('Admin/Entity/Edit', ['entity' => $entity]);
    }

    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        try {
            $entity->update($request->validated());
            return redirect()->route('admin.entity.index')->with('message', 'Updated successfully.');
        } catch (Throwable $e) {
            report($e);
            return redirect()->back()->withInput()->with('error', 'Update failed.');
        }
    }

    public function destroy(Entity $entity)
    {
        try {
            $entity->delete();
            return redirect()->route('admin.entity.index')->with('message', 'Deleted successfully.');
        } catch (Throwable $e) {
            report($e);
            return redirect()->route('admin.entity.index')->with('error', 'Deletion failed.');
        }
    }
}
```

---

## 4. Rutas y Breadcrumbs

### 4.1. Registrar Ruta
Abre `routes/admin.php` y añade la ruta de recurso junto a las demás:
```php
Route::resource('entity', 'EntityController');
```

### 4.2. Registrar Breadcrumbs
Abre `routes/breadcrumbs.php`. Esto es **obligatorio** para evitar el error `InvalidBreadcrumbException`.
Añade al final del archivo:
```php
Breadcrumbs::resource('admin.entity', 'Entities');
```

---

## 5. Vistas Frontend (Inertia / Vue)

Crea una carpeta en `resources/js/Pages/Admin/Entity/` y copia los 4 archivos fundamentales desde otro módulo que ya exista (como `Teacher` o `User`).

### 5.1. `Index.vue`
Requiere los componentes `<CardBox has-table>` y `<Pagination>`.
Asegúrate de:
- Inicializar `formDelete` (`useForm({})`) para el botón de eliminar.
- Modificar las rutas para apuntar a `admin.entity.create`, `admin.entity.edit`, etc.
- Hacer un `v-for="item in entities.data"`.

### 5.2. `Create.vue`
Requiere `<CardBox form>`.
- Mapear tu formulario reactivo: `const form = useForm({ name: '' })`.
- En el submit: `@submit.prevent="form.post(route('admin.entity.store'))"`.
- Utilizar los componentes visuales `<FormField>` y `<FormControl>`.

### 5.3. `Edit.vue`
Idéntico a `Create.vue` pero:
- Recibe la prop `entity`.
- Inicializa el form con los datos: `const form = useForm({ name: props.entity.name })`.
- En el submit usa método PUT: `@submit.prevent="form.put(route('admin.entity.update', entity.id))"`.

### 5.4. `Show.vue`
Es una vista de lectura. Usa una tabla HTML básica dentro de un `<CardBox>` para imprimir las propiedades: `{{ entity.name }}`.

---

## 6. Agregar al Menú Lateral (Seeder)

El menú de esta plantilla se carga dinámicamente desde la BD. Crea un Seeder para registrar tu módulo de forma automatizada:
```bash
php artisan make:seeder EntityMenuSeeder
```

Contenido:
```php
use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;

public function run()
{
    $menu = Menu::where('machine_name', 'admin')->first();

    if ($menu) {
        $exists = MenuItem::where('menu_id', $menu->id)
            ->where('uri', '/<admin>/entity')
            ->exists();

        if (! $exists) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'name' => 'Entities',
                'uri' => '/<admin>/entity',
                'enabled' => 1,
                'weight' => 10,
                'icon' => 'TU_ICONO_SVG_AQUI',
            ]);
        }
    }
}
```

Finalmente, corre el seeder:
```bash
php artisan db:seed --class=EntityMenuSeeder
```

¡Y listo! Ya tienes un módulo robusto, protegido y perfectamente integrado con la estética y arquitectura del proyecto.
