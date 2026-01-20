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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // Model class name
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->unsignedBigInteger('user_id')->nullable(); // User who performed the action
            $table->string('user_name')->nullable(); // User name snapshot
            $table->text('old_values')->nullable(); // JSON of old values (for updates/deletes)
            $table->text('new_values')->nullable(); // JSON of new values (for creates/updates)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
