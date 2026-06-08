<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Lien avec votre table de réservation existante (ajustez le nom si nécessaire)
            $table->foreignId('event_booking_id')->constrained()->cascadeOnDelete();

            // Informations du reçu
            $table->string('receipt_number')->unique(); // Exemple: REC-2026-0001
            $table->decimal('amount', 10, 2); // Montant payé
            $table->string('payment_method'); // cash, card, mobile_money, bank_transfer
            $table->string('status')->default('completed'); // pending, completed, refunded, failed

            // Suivi comptable précis
            $table->timestamp('paid_at')->useCurrent(); // Date et heure exacte de l'encaissement
            $table->foreignId('user_id')->nullable()->constrained(); // Caissier ou agent ayant encaissé
            $table->text('notes')->nullable(); // Commentaires éventuels
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
