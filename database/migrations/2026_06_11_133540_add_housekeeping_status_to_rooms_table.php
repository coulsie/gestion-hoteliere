<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Ajoute le champ de statut avec 'propre' par défaut
            $table->enum('housekeeping_status', ['propre', 'sale', 'en_cours', 'maintenance'])->default('propre');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('housekeeping_status');
        });
    }
};
