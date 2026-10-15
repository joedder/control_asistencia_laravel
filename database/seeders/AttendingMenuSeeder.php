<?php

namespace Database\Seeders;

use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;
use Illuminate\Database\Seeder;

class AttendingMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if (! $menu) {
            return;
        }

        MenuItem::updateOrCreate(
            ['menu_id' => $menu->id, 'uri' => '/<admin>/attending'],
            [
                'name' => 'Attendings',
                'enabled' => true,
                'weight' => 14,
                'icon' => 'M5,14H19V15H5V14M21,17V8H3V17H21M1,5H23V19H1V5M5,10H12V12H5V10Z',
            ]
        );
    }
}
