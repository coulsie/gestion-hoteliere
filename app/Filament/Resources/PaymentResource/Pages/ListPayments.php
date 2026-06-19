<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab; // Composant v5
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * 🔥 FORCE LA REINITIALISATION DE LA PAGINATION AU CHANGEMENT D'ONGLET
     * Évite que les lignes ne restent bloquées sur la page 2 invisible
     */
    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    /**
     * 🔥 SYNCHRONISATION ABSOLUE DES COMPTEURS TEXTUELS
     */
    public function getTabs(): array
    {
        $baseQuery = $this->getResource()::getEloquentQuery();

        return [
            // Onglet 1 : Global (84)
            'toutes' => Tab::make('🌐 Toutes les caisses')
                ->badge((clone $baseQuery)->count())
                ->badgeColor('gray'),

            // Onglet 2 : Hébergement (40)
            'hebergement' => Tab::make('🏨 Caisse Hébergement')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('receipt_number', 'LIKE', 'REC-%')
                    ->where('receipt_number', 'NOT LIKE', '%RESTO%')
                    ->where('receipt_number', 'NOT LIKE', '%SALLE%')
                )
                ->badge((clone $baseQuery)
                    ->where('receipt_number', 'LIKE', 'REC-%')
                    ->where('receipt_number', 'NOT LIKE', '%RESTO%')
                    ->where('receipt_number', 'NOT LIKE', '%SALLE%')
                    ->count()
                )
                ->badgeColor('success'),

            // Onglet 3 : Restaurant (34)
            'restauration' => Tab::make('🍽️ Caisse Restaurant')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('receipt_number', 'LIKE', '%RESTO%'))
                ->badge((clone $baseQuery)->where('receipt_number', 'LIKE', '%RESTO%')->count())
                ->badgeColor('warning'),

            // Onglet 4 : Salles (10)
            'salle' => Tab::make('🏢 Caisse Salles')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('receipt_number', 'LIKE', '%SALLE%'))
                ->badge((clone $baseQuery)->where('receipt_number', 'LIKE', '%SALLE%')->count())
                ->badgeColor('info'),
        ];
    }
}
