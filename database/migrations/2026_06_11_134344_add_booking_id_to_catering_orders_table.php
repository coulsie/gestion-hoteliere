<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On cible la table réelle de votre base de données
        Schema::table('catering_items', function (Blueprint $table) {
            $table->foreignId('event_booking_id')->nullable()->constrained('event_bookings')->nullOnDelete();

            // Si votre table n'a pas encore de colonne pour le prix total de la commande, décommentez la ligne suivante :
            // $table->decimal('total_amount', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('catering_items', function (Blueprint $table) {
            $table->dropForeign(['event_booking_id']);
            $table->dropColumn('event_booking_id');
        });
    }
};
