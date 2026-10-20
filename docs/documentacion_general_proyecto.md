# 📘 Documentación Técnica y Funcional del Proyecto: Control de Asistencia

> **Documento de Arquitectura, Funcionamiento y Personalización Visual**  
> **Versión del Sistema:** 1.0 (Laravel 12 + Inertia.js + Vue 3)  
> **Ubicación:** `docs/documentacion_general_proyecto.md`

---

## 📑 Tabla de Contenidos

1. [Resumen Ejecutivo del Proyecto](#1-resumen-ejecutivo-del-proyecto)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Estructura del Proyecto y Directorios Clave](#3-estructura-del-proyecto-y-directorios-clave)
4. [Módulos del Sistema y Lógica de Negocio](#4-módulos-del-sistema-y-lógica-de-negocio)
   - [4.1. Módulo de Asistencias (Núcleo)](#41-módulo-de-asistencias-núcleo)
   - [4.2. Módulo de Movimientos / Migraciones de Estudiantes](#42-módulo-de-movimientos--migraciones-de-estudiantes)
   - [4.3. Estructura Académica (Docentes, Grupos, Niveles, Categorías, Alumnos)](#43-estructura-académica-docentes-grupos-niveles-categorías-alumnos)
   - [4.4. Seguridad, Roles y Permisos Granulares](#44-seguridad-roles-y-permisos-granulares)
5. [Guía de Modificaciones para la Parte Visual (Frontend & UI)](#5-guía-de-modificaciones-para-la-parte-visual-frontend--ui)
   - [5.1. Arquitectura de Componentes y Vistas Vue](#51-arquitectura-de-componentes-y-vistas-vue)
   - [5.2. Personalización de Temas y Paleta de Colores (`colors.js`)](#52-personalización-de-temas-y-paleta-de-colores-colorsjs)
   - [5.3. Modo Oscuro (Dark Mode) y Estilos Globales](#53-modo-oscuro-dark-mode-y-estilos-globales)
   - [5.4. Menú de Navegación (Lateral y Superior)](#54-menú-de-navegación-lateral-y-superior)
   - [5.5. Estandarización de Tablas y Listados (Correlativo N° vs ID)](#55-estandarización-de-tablas-y-listados-correlativo-n-vs-id)
   - [5.6. Modificación y Estilos de Reportes PDF (DomPDF)](#56-modificación-y-estilos-de-reportes-pdf-dompdf)
6. [Flujo de Desarrollo y Comandos Habituales](#6-flujo-de-desarrollo-y-comandos-habituales)
7. [Buenas Prácticas para Nuevos Cambios](#7-buenas-prácticas-para-nuevos-cambios)

---

## 1. Resumen Ejecutivo del Proyecto

El **Sistema de Control de Asistencia** es una plataforma web integral diseñada para instituciones educativas y centros de formación. Su propósito principal es gestionar y auditar el ciclo completo de asistencia de los estudiantes por grupo y sesión de clase, permitiendo:

- Registro ágil y masivo de asistencias, inasistencias y justificaciones por jornada.
- Control de docentes titulares y docentes suplentes/sustitutos.
- Asignación jerárquica de categorías, niveles académicos, grupos y estudiantes.
- Migración y transferencias de estudiantes entre grupos con trazabilidad histórica por lotes.
- Generación de reportes ejecutivos e individuales en formato PDF.
- Seguridad basada en roles (`super-admin`, `administración`, `docente`) con aislamiento de visibilidad de datos según el rol asignado.

El sistema utiliza el paradigma **Monolito Híbrido SPA** a través de **Inertia.js**, combinando la robustez de Laravel en el backend con la reactividad y dinamismo fluido de Vue.js en el frontend, sin necesidad de construir una API REST desacoplada compleja.

---

## 2. Stack Tecnológico

| Capa | Tecnología | Versión / Detalle | Función Principal |
| :--- | :--- | :--- | :--- |
| **Backend** | **PHP** | `^8.2` | Motor de ejecución del servidor |
| **Framework Web** | **Laravel** | `^12.0` | Enrutamiento, ORM Eloquent, migraciones y controladores |
| **Integración SPA** | **Inertia.js** | Laravel `^2.0` / Vue `@inertiajs/vue3: ^1.0` | Conector entre Laravel y Vue sin recargas completas |
| **Frontend UI** | **Vue.js** | `^3.4` (Composition API, `<script setup>`) | Renderizado de vistas y componentes reactivos |
| **Gestión de Estado** | **Pinia** | `^2.0` | Gestión de estado global (Dark Mode, usuario actual) |
| **Estilos CSS** | **Tailwind CSS** | `^3.2` con plugins `@tailwindcss/forms` | Diseño responsivo, utilidades y modo oscuro |
| **Iconografía** | **@mdi/js** | `^7.0` (Material Design Icons) | Iconos SVG optimizados e importables individualmente |
| **PDF Reporting** | **Barryvdh DomPDF** | `^3.1` | Motor de renderizado de reportes PDF basados en Blade |
| **Permisos / ACL** | **Spatie Laravel Permission** | `^6.x` | Roles (`super-admin`, `administración`, `docente`) y permisos |
| **Empaquetador** | **Vite** | `^5.0` con `laravel-vite-plugin` | Compilación ultrarrápida de scripts y estilos |

---

## 3. Estructura del Proyecto y Directorios Clave

```text
control_asistencia_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/     # Controladores CRUD del panel administrativo
│   │   ├── Middleware/            # HasAccessAdmin, HandleInertiaAdminRequests, etc.
│   │   └── Requests/Admin/        # FormRequests de validación de datos
│   └── Models/                    # Modelos Eloquent (Attending, Student, Group, etc.)
├── config/
│   └── admin.php                  # Parámetros del panel (prefijos, paginación, roles)
├── database/
│   ├── migrations/                # Definición de tablas y campos de la BD
│   └── seeders/                   # Poblado inicial de roles, permisos y menús
├── docs/                          # Documentación del sistema
├── resources/
│   ├── css/                       # Estilos base, imports de Tailwind y complementos
│   ├── js/
│   │   ├── Components/            # Botones, Cards, Inputs, Modales, NavBars (UI)
│   │   ├── Layouts/               # LayoutAuthenticated.vue y LayoutGuest.vue
│   │   ├── Pages/Admin/           # Vistas Vue organizadas por módulo (CRUD)
│   │   ├── Stores/                # Tiendas Pinia (darkMode.js, main.js)
│   │   ├── colors.js              # Paleta cromática centralizada para componentes UI
│   │   └── menuNavBar.js          # Opciones de la barra superior de usuario
│   └── views/
│       ├── app.blade.php          # Plantilla raíz HTML donde monta Inertia
│       └── reports/               # Plantillas Blade para reportes PDF (DomPDF)
├── routes/
│   ├── admin.php                  # Rutas protegidas del panel administrativo
│   ├── breadcrumbs.php            # Migas de pan de cada recurso
│   └── web.php                    # Rutas públicas y de autenticación
└── tailwind.config.js             # Configuración de diseño y temas de Tailwind
```

---

## 4. Módulos del Sistema y Lógica de Negocio

### 4.1. Módulo de Asistencias (Núcleo)
- **Modelo:** `App\Models\Attending`  
- **Controlador:** `App\Http\Controllers\Admin\AttendingController`  
- **Vistas:** `resources/js/Pages/Admin/Attending/{Index, Create, Edit, Show}.vue`

#### Funcionamiento Clave:
1. **Agrupación por Sesión (`session_id` UUID):**  
   Al registrar la asistencia de un grupo con 20 o 30 alumnos, el sistema genera un identificador único UUID (`session_id`). Cada fila en la base de datos representa a un estudiante, pero todas comparten el mismo `session_id`, `id_group`, `id_teacher`, `substitute_teacher_id` y `class_date`.
2. **Listado Consolidado (`Index.vue`):**  
   El controlador realiza un `selectRaw` agrupado por `session_id` para mostrar cada sesión como un solo registro en la tabla general, evitando saturar la vista con cientos de registros individuales.
3. **Docente Sustituto / Suplente:**  
   El formulario permite seleccionar un docente sustituto opcional si el profesor titular no pudo impartir la clase.
4. **Estados de Asistencia:**  
   - `asistente`: El alumno estuvo presente.
   - `inasistente`: Falta no justificada.
   - `justificado`: Permite registrar un motivo en el campo `social_reason`.
5. **Aislamiento por Rol (Visibilidad Docente):**  
   - Si el usuario logueado es **Docente**, solo puede visualizar, crear o editar asistencias de los grupos que tiene a su cargo o en las que fue asignado como suplente.
   - Si es **Administrador** o **Super Admin**, tiene acceso y filtros globales de toda la institución.
6. **Exportación a PDF:**  
   - **Reporte Individual:** `GET /admin/attending/{attending}/pdf` genera la ficha de la sesión con estadísticas de presentes, ausentes, justificados y porcentajes de asistencia.
   - **Reporte General:** `GET /admin/attending/export-pdf` genera un informe consolidado filtrado por rangos de fecha, docente y grupo.

---

### 4.2. Módulo de Movimientos / Migraciones de Estudiantes
- **Modelo:** `App\Models\HistoryMovement`  
- **Controlador:** `App\Http\Controllers\Admin\HistoryMovementController`  
- **Vistas:** `resources/js/Pages/Admin/HistoryMovement/{Index, Create, Edit, Show}.vue`

#### Funcionamiento Clave:
- Permite transferir un grupo de alumnos desde un grupo origen (`id_group`) hacia un nuevo grupo destino (`id_new_group`).
- Las transferencias se agrupan por lotes mediante `batch_id` (UUID).
- Cuenta con un interruptor **`migrated` (Booleano)**:  
  Si está activado, en la misma transacción (`DB::transaction`) actualiza automáticamente el campo `id_group` del estudiante en la tabla `students`, asegurando integridad referencial inmediata.

---

### 4.3. Estructura Académica (Docentes, Grupos, Niveles, Categorías, Alumnos)

1. **Docentes (`Teacher`):** Almacena nombre, apellido, cédula/identificación (`identity_id`), nivel de inglés y enlace opcional con la cuenta de usuario (`id_user`).
2. **Categorías de Grupos (`CategoryGroup`):** Tipos o modalidades (ej. Regular, Intensivo, Sabatino).
3. **Niveles (`Level`):** Grado de avance curricular (ej. Básico 1, Intermedio A2, Avanzado).
4. **Grupos (`Group`):** Entidad centralizadora que une un nombre de grupo con su docente titular, su nivel académico y su categoría.
5. **Estudiantes (`Student`):** Registro de alumnos con sus datos personales y la llave foránea al grupo actual (`id_group`).

---

### 4.4. Seguridad, Roles y Permisos Granulares

El sistema utiliza Spatie Laravel Permission configurado con los siguientes roles predeterminados:

- **`super-admin`:** Acceso irrestricto a todas las funciones y configuraciones del sistema.
- **`administración`:** Gestión completa de usuarios, docentes, estudiantes, grupos, asistencias, exportaciones y movimientos.
- **`docente`:** Acceso restringido al listado de su entorno, toma de asistencias de sus propios grupos y edición autorizada.

> [!NOTE]
> Cada acción en el menú y controlador valida permisos específicos como `attending create`, `attending export single`, `student edit`, etc.

---

## 5. Guía de Modificaciones para la Parte Visual (Frontend & UI)

Toda la interfaz gráfica está desacoplada en componentes Vue dentro de la carpeta `resources/js/`. A continuación se detalla cómo modificar y extender la parte visual.

### 5.1. Arquitectura de Componentes y Vistas Vue

El diseño visual sigue una jerarquía clara:

```text
LayoutAuthenticated.vue (Estructura base: Sidebar + Navbar + Breadcrumb + Footer)
  └── SectionMain.vue (Contenedor con márgenes estándar)
        ├── SectionTitleLineWithButton.vue (Título de la vista e icono)
        └── CardBox.vue (Contenedor tipo tarjeta blanca/oscura para formularios o tablas)
              ├── Tablas HTML estilizadas con Tailwind
              └── FormField.vue / FormControl.vue (Campos e inputs)
```

#### Componentes UI Fundamentales (`resources/js/Components/`):
- **`<CardBox>`:** Contenedor primordial. Acepta atributos como `has-table` para tablas sin padding extra o `form` para encapsular formularios.
- **`<BaseButton>`:** Botón estandarizado. Acepta props:
  - `label`: Texto del botón.
  - `color`: Nombre del color (`info`, `success`, `danger`, `warning`, `white`, `contrast`).
  - `icon`: Objeto SVG de icono de `@mdi/js`.
  - `outline`: Booleano para estilo con borde sin fondo relleno.
  - `small`: Reduce el espaciado para botones dentro de tablas.
- **`<FormField>`:** Envuelve una etiqueta (`label`) y un mensaje de ayuda o error.
- **`<FormControl>`:** Entrada de datos (`input`, `select`, `textarea`). Soporta iconos laterales con la prop `icon`.
- **`<BaseIcon>`:** Renderizador de iconos vectoriales SVG con prop `:path="mdiIconName"`.

---

### 5.2. Personalización de Temas y Paleta de Colores (`colors.js`)

Todos los colores de botones, estados y alertas están centralizados en:  
📂 `resources/js/colors.js`

Si deseas cambiar el tono de los botones del sistema (por ejemplo, cambiar el azul de `info` por un tono corporativo personalizado o cambiar el verde de `success`):

1. Abre `resources/js/colors.js`.
2. Localiza el objeto `colorsBgLight`, `colorsOutline` o la función `getButtonColor`.
3. Ajusta las clases de Tailwind deseadas:

```javascript
// Ejemplo: Modificar el color primario/info para que use tonos violeta/índigo:
export const colorsBgLight = {
  // ...
  info: 'bg-indigo-600 border-indigo-600 text-white',
  success: 'bg-teal-500 border-teal-500 text-white'
}
```

Al modificar este archivo, **todos los botones y badges del sistema adoptarán la nueva identidad visual** sin tener que editar vista por vista.

---

### 5.3. Modo Oscuro (Dark Mode) y Estilos Globales

- El estado del modo oscuro es administrado por Pinia en `resources/js/Stores/darkMode.js`.
- Se activa agregando la clase `.dark` a la etiqueta `<html>`.
- Las variables y estilos complementarios están definidos en:
  - `resources/css/app.css`
  - `resources/css/styles/_basic.css`
  - `resources/css/_table.css`
- Para aplicar soporte de modo oscuro a cualquier elemento nuevo en Vue, simplemente usa el prefijo `dark:` de Tailwind:

```html
<div class="bg-white text-gray-800 dark:bg-slate-900 dark:text-slate-100">
  Contenido adaptativo
</div>
```

---

### 5.4. Menú de Navegación (Lateral y Superior)

#### Menú Lateral (Sidebar):
El menú lateral de esta plantilla es dinámico y se almacena en la base de datos (tablas `menus` y `menu_items`).
- Para agregar o renombrar un elemento del menú, puedes usar los seeders en `database/seeders/` (ej. `AttendingMenuSeeder.php`, `StudentMenuSeeder.php`) o gestionarlo desde la interfaz web en `/admin/menu`.
- Para cambiar los iconos del menú, se especifica el path SVG de `@mdi/js`.

#### Barra Superior (Navbar):
- Ubicación: `resources/js/menuNavBar.js`
- Contiene los enlaces al perfil del usuario, selector de Modo Claro/Oscuro y el botón de Cerrar Sesión.

---

### 5.5. Estandarización de Tablas y Listados (Correlativo N° vs ID)

Por usabilidad y experiencia de usuario, las tablas del sistema **no exponen el ID interno de la base de datos** al usuario final. En su lugar, muestran un número correlativo continuo calculado con base en la página actual de la paginación:

```vue
<!-- Cálculo estándar en las tablas Index.vue -->
<td data-label="N°">
  {{ (items.current_page - 1) * items.per_page + index + 1 }}
</td>
```

Para mantener la consistencia estética:
- Los encabezados de tabla usan `<th>N°</th>`.
- Los campos de documento de identidad usan la etiqueta `Identification` o `Cédula / Identificación`.

---

### 5.6. Modificación y Estilos de Reportes PDF (DomPDF)

Los reportes PDF no se renderizan con Vue; se procesan en el backend mediante Blade y la librería **DomPDF**:
- `resources/views/reports/attending-single.blade.php`: Reporte de una sesión individual.
- `resources/views/reports/attending-general.blade.php`: Reporte consolidado con filtros aplicados.

#### Pautas Críticas para Diseñar y Modificar los PDF:
> [!WARNING]
> DomPDF tiene soporte limitado para CSS moderno. No soporta CSS Grid (`display: grid`) ni Flexbox moderno con fluidez.

- **Usa tablas HTML (`<table>`) para la diagramación:** Toda la distribución en columnas (logos, encabezados, cajas métricas de resumen) debe construirse con tablas estándar con bordes colapsados (`border-collapse: collapse`).
- **Usa unidades en `pt`, `px` o `%`:** Evita `rem` o `vh`.
- **Márgenes de página:** Se configuran en la regla `@page { margin: 25px 30px; }`.
- **Colores:** Se definen con valores hexadecimales tradicionales (ej. `#2563eb` para azul primario, `#f8fafc` para fondos grises claros).

---

## 6. Flujo de Desarrollo y Comandos Habituales

### Puesta en marcha en Entorno Local:

1. **Terminal 1 - Servidor Frontend con HMR (Hot Module Replacement):**
   ```bash
   npm run dev
   ```
   *(En PowerShell de Windows, si hay bloqueo de scripts, ejecuta `npm.cmd run dev`)*.

2. **Terminal 2 - Servidor Backend Laravel:**
   ```bash
   php artisan serve
   ```
   Accede a: `http://127.0.0.1:8000`.

### Compilación para Producción (Build):
Cada vez que se prepare una actualización para producción o se desee probar el sitio sin tener Vite corriendo:
```bash
npm run build
```
Los archivos optimizados se generarán en la carpeta `public/build/`.

### Base de Datos y Seeders:
- **Ejecutar migraciones pendientes:**
  ```bash
  php artisan migrate
  ```
- **Reconstruir la base de datos completa con datos iniciales:**
  ```bash
  php artisan migrate:fresh --seed
  ```
- **Ejecutar un seeder específico:**
  ```bash
  php artisan db:seed --class=RolesAndPermissionsSeeder
  ```

### Solución de Problemas Comunes:
- **Pantalla en blanco o demora de 20-30 segundos al cargar:**  
  Ocurre si se utilizó un túnel de Cloudflare y la variable `ASSET_URL` quedó guardada con la URL expirada en el archivo `.env`.  
  *Solución:* Vaciar `ASSET_URL=` en el `.env` y ejecutar `php artisan config:clear`.
- **Los cambios en controladores o traducciones no se reflejan:**  
  ```bash
  php artisan optimize:clear
  ```

---

## 7. Buenas Prácticas para Nuevos Cambios

1. **Separación de Responsabilidades:** Nunca coloques lógica de validación masiva dentro del controlador; utiliza siempre un `FormRequest` en `app/Http/Requests/Admin/`.
2. **Breadcrumbs Obligatorios:** Si creas una nueva ruta de recurso en `routes/admin.php`, **debes registrarla** en `routes/breadcrumbs.php` mediante `Breadcrumbs::resource('admin.tu_modulo', 'Título');` para evitar excepciones de vista.
3. **Manejo de Excepciones:** Encapsula las operaciones en bloques `try { ... } catch (Throwable $e) { report($e); ... }` retornando mensajes amigables con `with('error', '...')`.
4. **Formularios Reactivos en Vue:** Utiliza el helper `useForm` de Inertia (`const form = useForm({ ... })`) para gestionar el envío, estados de carga (`form.processing`) y visualización de errores (`form.errors`).
