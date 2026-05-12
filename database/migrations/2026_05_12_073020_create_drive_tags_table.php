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
        Schema::create('drive_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('drive_file_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drive_file_id')->constrained('drive_files')->cascadeOnDelete();
            $table->foreignId('drive_tag_id')->constrained('drive_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['drive_file_id', 'drive_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drive_file_tag');
        Schema::dropIfExists('drive_tags');
    }
};
