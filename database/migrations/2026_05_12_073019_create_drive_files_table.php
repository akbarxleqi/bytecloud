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
        Schema::create('drive_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('drive_folders')->nullOnDelete();
            $table->foreignId('telegram_account_id')->nullable()->constrained('telegram_accounts')->nullOnDelete();
            $table->string('original_name');
            $table->string('stored_name')->nullable();
            $table->string('extension', 30)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->bigInteger('telegram_message_id')->nullable();
            $table->text('telegram_file_id')->nullable();
            $table->json('telegram_meta')->nullable();
            $table->string('tmp_path')->nullable();
            $table->string('preview_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->enum('visibility', ['private', 'shared'])->default('private');
            $table->enum('status', ['pending', 'uploading', 'uploaded', 'failed', 'deleted'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('preview_generated_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['folder_id', 'status']);
            $table->index('mime_type');
            $table->index('extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drive_files');
    }
};
