<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'agent qui ferme
            $table->date('closure_date')->unique(); // Une seule clôture par jour

            // Montants Théoriques (Calculés automatiquement par le système)
            $table->decimal('theoretical_cash', 12, 2)->default(0);
            $table->decimal('theoretical_mobile', 12, 2)->default(0);
            $table->decimal('theoretical_card', 12, 2)->default(0);

            // Montants Réels (Saisis physiquement par le réceptionniste)
            $table->decimal('real_cash', 12, 2)->default(0);
            $table->decimal('real_mobile', 12, 2)->default(0);
            $table->decimal('real_card', 12, 2)->default(0);

            // Écarts constatés
            $table->decimal('discrepancy', 12, 2)->default(0); // Différence totale

            $table->text('notes')->nullable(); // Justification obligatoire si écart
            $table->string('status')->default('clôturé'); // clôturé, validé_admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closures');
    }
};
