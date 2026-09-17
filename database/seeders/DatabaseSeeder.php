<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminCoreSeeder::class,
            TeacherMenuSeeder::class,
            CategoryGroupMenuSeeder::class,
            LevelMenuSeeder::class,
            GroupMenuSeeder::class,
            StudentMenuSeeder::class,
            AttendingMenuSeeder::class,
        ]);
    }
}
