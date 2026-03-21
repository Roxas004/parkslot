<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->dateTime('debut_reservation');

            // fin_reservation peut être :
            //   - calculée à la création (debut + durée par défaut du parking)
            //   - mise à jour si fermeture anticipée par user ou admin
            $table->dateTime('fin_reservation')->nullable();

            // Relation Prendre : un membre prend une réservation
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relation Reserver : une réservation occupe une place
            $table->foreignId('place_id')
                ->constrained('places')
                ->onDelete('cascade');

            // Relation Concerner : une réservation concerne une voiture
            $table->foreignId('voiture_id')
                ->constrained('voitures')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
