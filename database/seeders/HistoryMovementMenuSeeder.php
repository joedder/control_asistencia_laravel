<?php

namespace Database\Seeders;

use BalajiDharma\LaravelMenu\Models\Menu;
use BalajiDharma\LaravelMenu\Models\MenuItem;
use Illuminate\Database\Seeder;

class HistoryMovementMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::where('machine_name', 'admin')->first();

        if (! $menu) {
            return;
        }

        $item = MenuItem::updateOrCreate(
            ['menu_id' => $menu->id, 'uri' => '/<admin>/history-movement'],
            [
                'name' => 'History Movements',
                'enabled' => true,
                'weight' => 15,
                'icon' => 'M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12H17L21,16L25,12H22A10,10 0 0,0 12,2M11,7V12L15,14.5L16,12.8L13,11V7H11Z',
            ]
        );

        $item->syncRoles([
            config('admin.roles.super_admin'),
            'administración',
        ]);
    }
}
