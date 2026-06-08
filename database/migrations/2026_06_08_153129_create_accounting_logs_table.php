<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_logs', function (Blueprint $table) {
            $table->id();
            // Type de période comptable
            $table->string('period_type'); // daily, weekly, monthly

            // Dates de début et de fin de la période analysée
            $table->date('start_date');
            $table->date('end_date');

            // Indicateurs financiers calculés automatiquement
            $table->decimal('total_revenue', 12, 2)->default(0.00); // Total des encaissements réussis
            $table->integer('transactions_count')->default(0); // Nombre de reçus émis

            $table->string('status')->default('open'); // open, closed (pour verrouiller une période comptable)
            $table->timestamps();

            // Évite de générer deux rapports identiques pour une même journée/mois
            $table->unique(['period_type', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_logs');
    }
};
