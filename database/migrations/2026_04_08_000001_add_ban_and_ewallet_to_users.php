<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ban_type')->nullable()->after('registration_status'); // null, soft, hard
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('ban_expires_at')->nullable(); // null = permanent until admin lifts
            $table->text('ban_reason')->nullable();
            $table->string('ewallet_type')->nullable(); // Dana, GoPay, OVO, ShopeePay
            $table->string('ewallet_number')->nullable();
        });

        Schema::table('monthly_champions', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('reward_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ban_type', 'banned_at', 'ban_expires_at', 'ban_reason', 'ewallet_type', 'ewallet_number']);
        });

        Schema::table('monthly_champions', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
