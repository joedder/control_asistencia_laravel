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
            $exists = MenuItem::where('menu_id', $menu->id)
                ->where('uri', '/<admin>/student')
                ->exists();

            if (! $exists) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'name' => 'Students',
                    'uri' => '/<admin>/student',
                    'enabled' => 1,
                    'weight' => 13,
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-backpack" viewBox="0 0 16 16"><path d="M4.04 7.43a4 4 0 0 1 7.92 0 .5.5 0 1 1-.99.14 3 3 0 0 0-5.94 0 .5.5 0 1 1-.99-.14ZM4 9.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-4Zm1 .5v3h5v-3h-5Z"/><path d="M8 0a2 2 0 0 0-2 2H3.5a2 2 0 0 0-2 2v1c0 .52.198.993.523 1.354A8.464 8.464 0 0 0 1 11.127V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2.873a8.464 8.464 0 0 0-1.023-4.773A1.996 1.996 0 0 0 14.5 5V4a2 2 0 0 0-2-2H10a2 2 0 0 0-2-2Zm0 1a1 1 0 0 1 1 1h-2a1 1 0 0 1 1-1ZM3.5 3h9a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm8.365 3.553a7.468 7.468 0 0 1 1.135 4.574V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-2.873a7.468 7.468 0 0 1 1.135-4.574.5.5 0 0 1 .636-.206c.307.135.666.24.979.317.067.016.14.032.213.047.018-.11.042-.218.072-.32a3.5 3.5 0 1 1 5.93 0c.03.102.054.21.072.32.073-.015.146-.031.213-.047.313-.077.672-.182.98-.317a.5.5 0 0 1 .635.206Z"/></svg>',
                ]);
            }
        }
    }
}
