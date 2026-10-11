<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to drop unused boilerplate tables.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Forum tables (laravel-forum)
        Schema::dropIfExists('threads');

        // 2. Comments tables (laravel-comment)
        Schema::dropIfExists('comments');

        // 3. Reactions tables (laravel-reaction)
        Schema::dropIfExists('reactions');

        // 4. Viewable tables (laravel-viewable)
        Schema::dropIfExists('views');

        // 5. Attributes tables (laravel-attributes)
        Schema::dropIfExists('attributes');

        // 6. Activity log (spatie/laravel-activitylog)
        Schema::dropIfExists('activity_log');

        // 7. Media tables (laravel-media-manager & mediable)
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('media');

        // 8. Generic category tables (laravel-category) - Note: attendance system uses 'category_groups'
        Schema::dropIfExists('model_has_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_types');

        Schema::enableForeignKeyConstraints();

        // Remove the unused 'Categories' menu item from the sidebar navigation
        try {
            DB::table('menu_items')->where('uri', 'like', '%category/type%')->delete();
        } catch (\Throwable $e) {
            // Ignore if table or record does not exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Unused legacy tables are not recreated on rollback
    }
};
