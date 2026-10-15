# Comandos de desarrollo, compilación y base de datos

Esta guía reúne los comandos habituales para trabajar con el Sistema de Control de Asistencia. Ejecútalos desde la carpeta raíz del proyecto.

## Requisitos

- PHP 8.2 o superior y Composer.
- Node.js y npm.
- MySQL configurado en `.env`.

En PowerShell de Windows, si `npm` muestra un error de política de ejecución sobre `npm.ps1`, utiliza `npm.cmd` en los comandos indicados.

## Iniciar el proyecto

### Desarrollo de frontend

Abre una terminal en la carpeta del proyecto y ejecuta:

```bash
npm run dev
```

Deja esa terminal abierta. Vite sirve los recursos de Vue y CSS y detecta cambios para desarrollo. En otra terminal ejecuta:

```bash
php artisan serve
```

Abre `http://127.0.0.1:8000`. En PowerShell puedes usar `npm.cmd run dev` si `npm run dev` queda bloqueado por la política de scripts.

### Usar recursos compilados

Para ejecutar el sistema sin el servidor de desarrollo de Vite, compila los recursos y luego inicia Laravel:

```bash
npm run build
php artisan serve
```

En PowerShell, el primer comando también puede ejecutarse como `npm.cmd run build`. Laravel servirá los archivos generados en `public/build`. No hace falta mantener `npm run dev` activo.

## Cuándo compilar (`build`)

Ejecuta `npm run build` cuando quieras generar o actualizar los archivos frontend optimizados que utiliza Laravel sin Vite en desarrollo, por ejemplo, para probar el modo compilado o preparar una publicación. Cada vez que cambies componentes Vue, CSS, JavaScript u otros recursos del frontend y quieras ver esos cambios en modo compilado, vuelve a ejecutar el build.

Durante el desarrollo cotidiano, usa `npm run dev`; no necesitas compilar después de cada cambio mientras Vite está ejecutándose. Después de compilar, inicia o reinicia `php artisan serve` si es necesario y recarga el navegador.

## Migraciones

Aplicar solo migraciones pendientes sin borrar datos:

```bash
php artisan migrate
```

Consultar el estado de las migraciones:

```bash
php artisan migrate:status
```

### Restablecer toda la base de datos

El siguiente comando elimina todas las tablas y datos, vuelve a ejecutar todas las migraciones y luego ejecuta todos los seeders registrados en `DatabaseSeeder`:

```bash
php artisan migrate:fresh --seed
```

**Advertencia:** úsalo solo cuando realmente quieras borrar la base de datos local. No lo ejecutes en una base con información que necesites conservar.

## Seeders del proyecto

El comando para ejecutar todos los seeders registrados es:

```bash
php artisan db:seed
```

`DatabaseSeeder` ejecuta, en este orden, los seeders actuales:

1. `RolesAndPermissionsSeeder`: crea/sincroniza roles y permisos.
2. `AdminTagTypeSeeder`: registra los tipos de etiquetas administrativos.
3. `AdminCoreSeeder`: datos base del panel administrativo.
4. `TeacherMenuSeeder`: menú del módulo de docentes.
5. `CategoryGroupMenuSeeder`: menú de categorías de grupos.
6. `LevelMenuSeeder`: menú de niveles.
7. `GroupMenuSeeder`: menú de grupos.
8. `StudentMenuSeeder`: menú de estudiantes.
9. `AttendingMenuSeeder`: menú de asistencias.
10. `HistoryMovementMenuSeeder`: menú de historial de movimientos.

Ejecutar un seeder específico, sin volver a sembrar todos los demás:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=AdminTagTypeSeeder
php artisan db:seed --class=AdminCoreSeeder
php artisan db:seed --class=TeacherMenuSeeder
php artisan db:seed --class=CategoryGroupMenuSeeder
php artisan db:seed --class=LevelMenuSeeder
php artisan db:seed --class=GroupMenuSeeder
php artisan db:seed --class=StudentMenuSeeder
php artisan db:seed --class=AttendingMenuSeeder
php artisan db:seed --class=HistoryMovementMenuSeeder
```

Los seeders que se usan conjuntamente para roles y permisos son `RolesAndPermissionsSeeder` y `AdminCoreSeeder`; para poblar la configuración completa del proyecto, usa `php artisan db:seed`.

## Comandos habituales resumidos

| Necesidad | Comandos |
|---|---|
| Desarrollar frontend y abrir la aplicación | Terminal 1: `npm run dev`; terminal 2: `php artisan serve` |
| Ejecutar usando los recursos compilados | `npm run build`; después `php artisan serve` |
| Aplicar migraciones nuevas conservando datos | `php artisan migrate` |
| Ejecutar todos los seeders | `php artisan db:seed` |
| Reconstruir la base desde cero y sembrar | `php artisan migrate:fresh --seed` |


## Comando a aplicar para tunelizar
aplicar primero el de laragon luego aplicar el de powershell y cambiar en .env la url generada en el powershell

## comando para aplicar en laragon para hostear el local
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
## comado para hacer un tunelizacion del proyecto en powershell como admin aplicar este comando
```bash
cloudflared tunnel --protocol http2 --url http://127.0.0.1:8000
```