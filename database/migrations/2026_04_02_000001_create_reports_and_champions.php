<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('reported_user_id');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('monthly_champions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('month');
            $table->integer('year');
            $table->integer('points')->default(0);
            $table->integer('reward_amount')->nullable(); // Rp amount
            $table->string('reward_status')->default('pending'); // pending, paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['month', 'year']); // Only 1 champion per month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_champions');
        Schema::dropIfExists('user_reports');
    }
};
