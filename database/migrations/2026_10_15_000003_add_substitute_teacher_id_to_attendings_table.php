<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendings', 'substitute_teacher_id')) {
            Schema::table('attendings', function (Blueprint $table) {
                $table->unsignedInteger('substitute_teacher_id')->nullable()->after('id_teacher');
                $table->foreign('substitute_teacher_id')
                    ->references('id')
                    ->on('teachers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendings', 'substitute_teacher_id')) {
            Schema::table('attendings', function (Blueprint $table) {
                $table->dropForeign(['substitute_teacher_id']);
                $table->dropColumn('substitute_teacher_id');
            });
        }
    }
};
