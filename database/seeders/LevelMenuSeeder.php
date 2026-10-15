<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;

class LevelMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if ($menu) {
            MenuItem::updateOrCreate(
                ['menu_id' => $menu->id, 'uri' => '/<admin>/level'],
                [
                    'menu_id' => $menu->id,
                    'name' => 'Levels',
                    'uri' => '/<admin>/level',
                    'enabled' => 1,
                    'weight' => 11,
                    'icon' => 'M9,5V9H21V5M9,19H21V15H9M9,14H21V10H9M4,9H8V5H4M4,19H8V15H4M4,14H8V10H4V14Z',
                ]
            );
        }
    }
}
