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
        Schema::table('bonuses_penalties', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('reason'); // 1 = pending approval, 2 = applied
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonuses_penalties', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by']);
        });
    }
};
