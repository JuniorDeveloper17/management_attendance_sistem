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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('karyawans')->cascadeOnDelete();
            $table->string('checkin_location');
            $table->string('checkout_location');
            $table->string('checkin_image');
            $table->string('checkout_image');
            $table->string('durasi_kerja');
            $table->string('id_device')->nullable()->default(null);
            $table->foreignId('id_office')->constrained('offices')->noActionOnDelete();
            $table->foreignId('id_shift')->constrained('shifts')->noActionOnDelete();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
