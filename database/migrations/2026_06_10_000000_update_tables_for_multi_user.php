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
        if (!Schema::hasColumn('telegram_accounts', 'user_id')) {
            Schema::table('telegram_accounts', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('drive_folders', 'telegram_account_id')) {
            Schema::table('drive_folders', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['parent_id']);
                
                // Drop old unique constraint
                $table->dropUnique(['parent_id', 'slug']);
                
                // Add telegram_account_id
                $table->foreignId('telegram_account_id')->nullable()->after('parent_id')->constrained('telegram_accounts')->nullOnDelete();
                
                // Add new unique constraint scoped to telegram_account_id
                $table->unique(['telegram_account_id', 'parent_id', 'slug']);
                
                // Add separate index on parent_id for foreign key performance
                $table->index('parent_id');
                
                // Recreate foreign key
                $table->foreign('parent_id')->references('id')->on('drive_folders')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('drive_folders', 'telegram_account_id')) {
            Schema::table('drive_folders', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropIndex(['parent_id']);
                $table->dropUnique(['telegram_account_id', 'parent_id', 'slug']);
                $table->dropColumn('telegram_account_id');
                
                $table->unique(['parent_id', 'slug']);
                $table->foreign('parent_id')->references('id')->on('drive_folders')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('telegram_accounts', 'user_id')) {
            Schema::table('telegram_accounts', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
