<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_items', function (Blueprint $blueprint) {
            // Ajoute le stock actuel et le seuil d'alerte (ex: alerte s'il reste moins de 5 bouteilles)
            $blueprint->integer('stock_quantity')->default(0)->after('unit_price');
            $blueprint->integer('alert_threshold')->default(5)->after('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('catering_items', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['stock_quantity', 'alert_threshold']);
        });
    }
};
