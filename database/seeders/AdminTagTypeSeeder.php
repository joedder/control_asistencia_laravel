<?php

namespace Database\Seeders;

use BalajiDharma\LaravelCategory\Models\CategoryType;
use Illuminate\Database\Seeder;

class AdminTagTypeSeeder extends Seeder
{
    public function run(): void
    {
        CategoryType::query()->firstOrCreate(
            ['machine_name' => config('admin.tag_name', 'admin_tag')],
            ['name' => 'Admin Tags', 'description' => 'Internal tags used by the admin panel', 'is_flat' => true]
        );
    }
}
