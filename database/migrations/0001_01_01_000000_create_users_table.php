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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nisn')->unique();
        $table->string('name');
        $table->string('kelas');
        $table->string('phone');
        $table->date('tanggal_lahir'); // ✅ TAMBAH INI
        $table->string('jenis_kelamin');
        $table->string('photo');
        $table->string('password');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
