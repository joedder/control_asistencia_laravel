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
        Schema::create('attendings', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('id_teacher');
            $table->foreign('id_teacher')->references('id')->on('teachers');
            
            $table->unsignedBigInteger('id_student');
            $table->foreign('id_student')->references('id')->on('students');
            
            $table->unsignedBigInteger('id_group');
            $table->foreign('id_group')->references('id')->on('groups');
            
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            
            $table->enum('status', ['asistente', 'inasistente', 'justificado']);
            $table->text('social_reason')->nullable();
            $table->dateTime('class_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendings');
    }
};
