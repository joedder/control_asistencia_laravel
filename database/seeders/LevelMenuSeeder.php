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
            $exists = MenuItem::where('menu_id', $menu->id)
                ->where('uri', '/<admin>/level')
                ->exists();

            if (! $exists) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'name' => 'Levels',
                    'uri' => '/<admin>/level',
                    'enabled' => 1,
                    'weight' => 11,
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-steps" viewBox="0 0 16 16"><path d="M.5 0a.5.5 0 0 1 .5.5v15a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 .5 0M2 1.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5z"/></svg>',
                ]);
            }
        }
    }
}
