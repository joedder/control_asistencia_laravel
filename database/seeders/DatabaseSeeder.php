<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\Activity;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Activity::withoutLogs(function () {
            $this->call([
                AdminCoreSeeder::class,
                ModulePermissionsSeeder::class,
                TeacherMenuSeeder::class,
                CategoryGroupMenuSeeder::class,
                LevelMenuSeeder::class,
                GroupMenuSeeder::class,
                StudentMenuSeeder::class,
                AttendingMenuSeeder::class,
            ]);
        });
    }
}
