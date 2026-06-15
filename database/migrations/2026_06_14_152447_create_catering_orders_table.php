<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Ex: CMD-20260612-XXXX

            // Lien vers l'hôtel (Optionnel : si rempli, on ajoute à la note de la chambre - Scénario A)
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            // Si client externe (Scénario B)
            $table->string('client_name')->default('Client Comptoir');

            // Détails financiers et statut
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->timestamps();
        });

        // Table pivot pour gérer les articles dans une commande (Plusieurs articles par commande)
        Schema::create('catering_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_order_id')->constrained('catering_orders')->cascadeOnDelete();
            $table->foreignId('catering_item_id')->constrained('catering_items'); // Lien vers votre carte
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Prix au moment de la commande
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_order_items');
        Schema::dropIfExists('catering_orders');
    }
};
