# Guía de Comandos para Migraciones y Seeders

Este documento contiene los comandos esenciales para administrar la base de datos de la aplicación, restaurar tablas y ejecutar los seeders, tanto de forma global como individual.

---

## 1. Refrescar TODO (Borrón y cuenta nueva)

Este comando borrará todas las tablas existentes en la base de datos, volverá a ejecutar todas las migraciones desde cero y, finalmente, ejecutará el archivo principal `DatabaseSeeder.php` (el cual llama a todos los seeders registrados).

**⚠️ Advertencia:** Este comando eliminará todos los datos de prueba y registros actuales. Úsalo solo en entornos de desarrollo local.

```bash
php artisan migrate:fresh --seed
```

---

## 2. Correr TODOS los seeders (Sin borrar las tablas)

Si ya tienes tus tablas creadas y solo quieres poblar los datos (y menús) ejecutando el `DatabaseSeeder.php` completo, utiliza:

```bash
php artisan db:seed
```

---

## 3. Ejecutar los seeders de manera individual

Si creaste una nueva tabla o módulo y **no quieres borrar los datos existentes**, puedes correr únicamente el seeder específico de ese módulo. 

A continuación, la lista de los seeders individuales que hemos ido creando módulo por módulo:

**Usuarios, Roles, Permisos y Menú Principal (Administrador):**
```bash
php artisan db:seed --class=AdminCoreSeeder
```

**Módulo de Profesores (Teachers):**
```bash
php artisan db:seed --class=TeacherMenuSeeder
```

**Módulo de Categorías de Grupo (Category Groups):**
```bash
php artisan db:seed --class=CategoryGroupMenuSeeder
```

**Módulo de Niveles (Levels):**
```bash
php artisan db:seed --class=LevelMenuSeeder
```

**Módulo de Grupos (Groups):**
```bash
php artisan db:seed --class=GroupMenuSeeder
```

**Módulo de Estudiantes (Students):**
```bash
php artisan db:seed --class=StudentMenuSeeder
```

**Módulo de Asistencias (Attendings):**
```bash
php artisan db:seed --class=AttendingMenuSeeder
```

---

## Notas Adicionales
- Para que estos comandos funcionen correctamente, tu entorno de consola debe estar utilizando la versión de **PHP 8.3** (o superior), tal como lo requiere el framework del proyecto actual.
- Si solo deseas crear nuevas tablas de migraciones que estén pendientes sin alterar los datos, puedes correr: `php artisan migrate`.
