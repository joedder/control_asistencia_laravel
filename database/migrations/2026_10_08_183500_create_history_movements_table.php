<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_student')->constrained('students')->onDelete('cascade');
            $table->foreignId('id_group')->constrained('groups')->onDelete('cascade');
            $table->foreignId('id_new_group')->constrained('groups')->onDelete('cascade');
            $table->boolean('migrated')->default(false);
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_movements');
    }
};
