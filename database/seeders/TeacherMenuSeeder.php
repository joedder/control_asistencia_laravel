<?php

namespace Database\Seeders;

use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;
use Illuminate\Database\Seeder;

class TeacherMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el menú 'admin'
        $menu = Menu::where('machine_name', 'admin')->first();

        if ($menu) {
            // Verificar si el MenuItem ya existe
            $teacherMenuItemExists = MenuItem::where('menu_id', $menu->id)
                ->where('uri', '/<admin>/teacher')
                ->exists();

            if (! $teacherMenuItemExists) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'name' => 'Teachers',
                    'uri' => '/<admin>/teacher',
                    'enabled' => 1,
                    'weight' => 10, // Puedes ajustar el orden aquí
                    // Icono de un profesor o cuenta (mdiAccountTie o similar en la fuente de la plantilla)
                    'icon' => 'M16 17V19H2V17S2 13 9 13 16 17 16 17M12.5 7.5A3.5 3.5 0 1 0 9 11A3.5 3.5 0 0 0 12.5 7.5M15.94 13A5.32 5.32 0 0 1 18 17V19H22V17S22 13.37 15.94 13M15 4A3.39 3.39 0 0 0 13.07 4.59A5 5 0 0 1 13.07 10.41A3.39 3.39 0 0 0 15 11A3.5 3.5 0 0 0 15 4Z', 
                ]);
            }
        }
    }
}
