<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('category.table_names');
        $columnNames = config('category.column_names');

        if (! Schema::hasTable($tableNames['category_types'])) {
            Schema::create($tableNames['category_types'], function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('machine_name', 64)->unique();
                $table->boolean('is_flat')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($tableNames['categories'])) {
            Schema::create($tableNames['categories'], function (Blueprint $table) use ($tableNames) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('category_type_id');
                $table->foreign('category_type_id')->references('id')->on($tableNames['category_types'])->onDelete('cascade');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->unique(['category_type_id', 'slug']);
                $table->string('description')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('weight')->default(0);
                $table->text('icon')->nullable();
                $table->string('color', 50)->nullable();
                $table->boolean('enabled')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($tableNames['model_has_categories'])) {
            Schema::create($tableNames['model_has_categories'], function (Blueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger('category_id');
                $table->foreign('category_id')->references('id')->on($tableNames['categories'])->onDelete('cascade');
                $table->morphs('model');
                $table->integer('weight')->default(0);
                $table->boolean('is_default')->default(false);
                $table->unsignedBigInteger('category_type_id');
                $table->primary(['category_id', $columnNames['model_morph_key'], 'model_type']);
            });
        }
    }

    public function down(): void
    {
        // Keep these tables: laravel-admin-core requires them for its admin tags.
    }
};
