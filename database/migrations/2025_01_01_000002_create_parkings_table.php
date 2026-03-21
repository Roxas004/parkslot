<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->string('ville_parking');
            $table->string('lib_parking');
            // Durée par défaut des réservations en minutes, définie par l'admin
            $table->unsignedInteger('duree_reservation_defaut')->default(480); // 8h par défaut
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
