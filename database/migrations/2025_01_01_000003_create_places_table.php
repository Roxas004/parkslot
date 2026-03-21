<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            // Ex : "A1", "B12" — unique au sein d'un même parking
            $table->string('num_place');
            $table->boolean('disponible')->default(true);
            $table->foreignId('parking_id')
                ->constrained('parkings')
                ->onDelete('cascade');
            $table->timestamps();

            // Un numéro de place doit être unique dans un parking donné
            $table->unique(['num_place', 'parking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
