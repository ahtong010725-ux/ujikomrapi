<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('image')->nullable()->after('message');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('registration_status');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
