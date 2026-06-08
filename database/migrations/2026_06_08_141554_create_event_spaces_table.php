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
        Schema::create('event_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex: Salle Grand Baobab, Amphithéâtre
            $table->string('type'); // reunion, spectacle, seminaire, restaurant
            $table->integer('capacity'); // Nombre de places maximales
            $table->decimal('hourly_rate', 10, 2); // Tarif horaire pour les séminaires/réunions
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_spaces');
    }
};
