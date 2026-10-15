<?php

namespace Database\Seeders;

use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;
use Illuminate\Database\Seeder;

class CategoryGroupMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if (! $menu) {
            return;
        }

        MenuItem::updateOrCreate(
            ['menu_id' => $menu->id, 'uri' => '/<admin>/category-group'],
            [
                'name' => 'Category Groups',
                'enabled' => true,
                'weight' => 10,
                'icon' => 'M5,9.5L7.5,14H2.5L5,9.5M3,4H7V8H3V4M5,20A2,2 0 0,0 7,18A2,2 0 0,0 5,16A2,2 0 0,0 3,18A2,2 0 0,0 5,20M9,5V7H21V5H9M9,19H21V17H9V19M9,13H21V11H9V13Z',
            ]
        );
    }
}
