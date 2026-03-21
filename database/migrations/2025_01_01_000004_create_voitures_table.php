<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voitures', function (Blueprint $table) {
            $table->id();
            $table->string('modele_voiture');
            $table->string('immatriculation')->unique();

            // Position dans la file d'attente (null = pas en attente)
            $table->unsignedInteger('place_attente')->nullable();

            // Relation Posseder : chaque voiture appartient à un membre
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relation Cif : parking pour lequel la voiture est en file d'attente
            // Nullable car la voiture n'est pas forcément en attente
            $table->foreignId('parking_id')
                ->nullable()
                ->constrained('parkings')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voitures');
    }
};
