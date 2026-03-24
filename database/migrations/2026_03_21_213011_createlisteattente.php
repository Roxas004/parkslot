<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_attente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voiture_id');
            $table->unsignedBigInteger('parking_id');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->foreign('voiture_id')->references('id')->on('voitures')->onDelete('cascade');
            $table->foreign('parking_id')->references('id')->on('parkings')->onDelete('cascade');

            $table->unique(['voiture_id', 'parking_id']);
            $table->index(['parking_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_attente');
    }
};
