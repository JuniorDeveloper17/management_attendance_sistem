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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('email');
            $table->string('password')->default(Hash::make('Univbi12345'));
            $table->longText('alamat')->nullable()->default(null);
            $table->string('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->default(null);
            $table->string('foto')->nullable()->default(null);
            $table->string('no_telp')->unique();
            $table->boolean('wfa')->nullable()->default(false);
            $table->string('status')->default('Aktif');
            $table->longText('face_id')->nullable()->default(null);
            $table->string('id_device')->nullable()->default(null);
            $table->foreignId('id_shift')->constrained('shifts')->noActionOnDelete();
            $table->foreignId('id_office')->constrained('offices')->noActionOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
        Schema::dropIfExists('izins');
        Schema::dropIfExists('attendances');
    }
};
