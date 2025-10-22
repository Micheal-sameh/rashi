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
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->dropColumn(['title', 'message', 'type', 'subject_type', 'subject_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropForeign(['notification_id']);
            $table->dropColumn('notification_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->morphs('subject');
            $table->json('data')->nullable();
        });
    }
};
