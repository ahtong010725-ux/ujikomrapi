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
        Schema::table('lost_items', function (Blueprint $table) {
            $table->string('status')->default('lost')->after('description'); // e.g., lost, resolved
        });

        Schema::table('found_items', function (Blueprint $table) {
            $table->string('status')->default('found')->after('description'); // e.g., found, resolved
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('found_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
