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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedInteger('id_teacher');
            $table->foreign('id_teacher')->references('id')->on('teachers');
            $table->unsignedBigInteger('id_category_group');
            $table->foreign('id_category_group')->references('id')->on('category_groups');
            $table->unsignedBigInteger('id_level');
            $table->foreign('id_level')->references('id')->on('levels');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
