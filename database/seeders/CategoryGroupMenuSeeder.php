<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;

class CategoryGroupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if ($menu) {
            $exists = MenuItem::where('menu_id', $menu->id)
                ->where('uri', '/<admin>/category-group')
                ->exists();

            if (! $exists) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'name' => 'Category Groups',
                    'uri' => '/<admin>/category-group',
                    'enabled' => 1,
                    'weight' => 10,
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-task" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5zM3 3H2v1h1z"/><path d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1z"/><path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5zM2 7h1v1H2zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm1 .5H2v1h1z"/></svg>',
                ]);
            }
        }
    }
}
