<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions\Action; // 🔥 IMPORTATION DE L'ACTION DE PAGE GLOBALE V5
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon; // 🔥 IMPORTATION DE L'ICÔNE COLORÉE V5
use Symfony\Component\HttpFoundation\StreamedResponse; // 🔥 POUR LE TÉLÉCHARGEMENT COMPTABILISÉ

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. 🔥 BOUTON D'EXPORTATION FINANCIÈRE GLOBALE (Placé en haut de la page)
            Action::make('export_financials')
                ->label('Exporter Rapport (CSV)')
                ->icon(Heroicon::OutlinedDocumentArrowDown) // Icône de téléchargement v5
                ->color('info') // Couleur bleue éclatante style Bootstrap
                ->action(function (): StreamedResponse {
                    // Récupère les données filtrées en conservant l'ordre et le regroupement de l'écran
                    $records = $this->getFilteredTableQuery()->get();

                    return response()->streamDownload(function () use ($records) {
                        $handle = fopen('php://output', 'w');

                        // Injection du BOM UTF-8 indispensable pour qu'Excel sous Windows lise correctement le symbole F CFA
                        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                        // Ligne d'en-tête de votre tableur comptable
                        fputcsv($handle, [
                            'Date d\'encaissement',
                            'N° Reçu',
                            'Type de Caisse',
                            'Client',
                            'Mode de Règlement',
                            'Montant Encaissé (F CFA)'
                        ], ';');

                        // Boucle d'écriture de vos recettes d'hôtel (Chambre, Resto, Salle)
                        foreach ($records as $payment) {
                            fputcsv($handle, [
                                $payment->created_at?->format('d/m/Y H:i') ?? 'N/A',
                                $payment->receipt_number ?? '#00' . $payment->id,
                                strtoupper($payment->payment_type ?? 'Chambre'),
                                $payment->booking?->customer_name ?? $payment->eventBooking?->customer_name ?? 'Client de passage',
                                strtoupper($payment->payment_method ?? 'Espèces'),
                                $payment->amount
                            ], ';');
                        }

                        fclose($handle);
                    }, 'Rapport_Financier_' . now()->format('d_m_Y') . '.csv');
                }),

            // 2. Votre bouton "Créer" existant
            CreateAction::make(),
        ];
    }
}
