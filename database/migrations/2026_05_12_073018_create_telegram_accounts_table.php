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
        Schema::create('telegram_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('api_id');
            $table->text('api_hash');
            $table->string('phone_number')->nullable();
            $table->string('session_name')->default('default');
            $table->string('session_path')->nullable();
            $table->enum('default_chat_type', ['saved_messages', 'private_channel'])->default('saved_messages');
            $table->string('default_chat_id')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->timestamp('last_connected_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_accounts');
    }
};
