<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;

class StudentMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if ($menu) {
            MenuItem::updateOrCreate(
                ['menu_id' => $menu->id, 'uri' => '/<admin>/student'],
                [
                    'menu_id' => $menu->id,
                    'name' => 'Students',
                    'uri' => '/<admin>/student',
                    'enabled' => 1,
                    'weight' => 13,
                    'icon' => 'M16 8C16 10.21 14.21 12 12 12C9.79 12 8 10.21 8 8L8.11 7.06L5 5.5L12 2L19 5.5V10.5H18V6L15.89 7.06L16 8M12 14C16.42 14 20 15.79 20 18V20H4V18C4 15.79 7.58 14 12 14Z',
                ]
            );
        }
    }
}
