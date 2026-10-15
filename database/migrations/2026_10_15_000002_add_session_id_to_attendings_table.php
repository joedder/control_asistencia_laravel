<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendings', 'session_id')) {
            Schema::table('attendings', function (Blueprint $table) {
                $table->uuid('session_id')->nullable()->index()->after('id');
            });
        }

        // Existing lists were stored without a session identifier. Group each
        // legacy list by its exact creation timestamp so same-day lists remain distinct.
        DB::table('attendings')
            ->select('id_group', 'id_teacher', 'class_date', 'created_at')
            ->whereNull('session_id')
            ->groupBy('id_group', 'id_teacher', 'class_date', 'created_at')
            ->orderBy('id_group')
            ->orderBy('class_date')
            ->orderBy('created_at')
            ->get()
            ->each(function ($legacySession): void {
                $sessionId = (string) \Illuminate\Support\Str::uuid();

                DB::table('attendings')
                    ->where('id_group', $legacySession->id_group)
                    ->where('id_teacher', $legacySession->id_teacher)
                    ->where('class_date', $legacySession->class_date)
                    ->where('created_at', $legacySession->created_at)
                    ->whereNull('session_id')
                    ->update(['session_id' => $sessionId]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendings', 'session_id')) {
            Schema::table('attendings', function (Blueprint $table) {
                $table->dropIndex(['session_id']);
                $table->dropColumn('session_id');
            });
        }
    }
};
