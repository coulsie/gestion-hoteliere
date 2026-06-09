<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Clé étrangère liée à la table key_cards (créée à l'étape précédente)
            $table->foreignId('key_card_id')
                ->nullable()
                ->after('room_id') // Positionne la colonne après l'ID de la chambre (ajustez si nécessaire)
                ->constrained()
                ->nullOnDelete();

            // Suivi des dates pour la sécurité d'accès
            $table->timestamp('key_card_assigned_at')->nullable()->after('key_card_id');
            $table->timestamp('key_card_expires_at')->nullable()->after('key_card_assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Suppression de la contrainte de clé étrangère puis des colonnes
            $table->dropForeign(['key_card_id']);
            $table->dropColumn(['key_card_id', 'key_card_assigned_at', 'key_card_expires_at']);
        });
    }
};

