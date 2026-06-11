<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_bookings', function (Blueprint $table) {
            // Enregistre si c'est loué à l'heure, à la période ou à la journée
            $table->string('formule_location')->default('journee')->after('event_space_id');
            // Enregistre si c'est le matin, l'après-midi ou le soir (null si journée complète)
            $table->string('choix_periode')->nullable()->after('formule_location');
            // Enregistre le nombre d'heures (null si forfait)
            $table->integer('nombre_heures')->nullable()->after('choix_periode');
        });
    }

    public function down(): void
    {
        Schema::table('event_bookings', function (Blueprint $table) {
            $table->dropColumn(['formule_location', 'choix_periode', 'nombre_heures']);
        });
    }
};
