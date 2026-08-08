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
        Schema::create('report_schedule_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('report_schedules')->cascadeOnDelete();
            $table->string('status');
            $table->integer('row_count')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('ran_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_schedule_logs');
    }
};
