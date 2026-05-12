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
        if (Schema::hasTable('drive_download_logs')) {
            Schema::table('drive_download_logs', function (Blueprint $table) {
                $table->foreign('drive_file_id')
                    ->references('id')
                    ->on('drive_files')
                    ->nullOnDelete();
            });

            return;
        }

        Schema::create('drive_download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drive_file_id')->nullable()->constrained('drive_files')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('download_type')->default('private');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drive_download_logs');
    }
};
