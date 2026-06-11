<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement - {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 40px; font-size: 14px; }
        .receipt-box { max-w: 800px; margin: auto; border: 1px solid #eee; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); }
        .header { display: flex; justify-content: space-between; border-b: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #0d6efd; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .details-table th { background: #f8f9fa; text-align: left; padding: 10px; border-bottom: 2px solid #dee2e6; font-weight: bold; }
        .details-table td { padding: 12px 10px; border-bottom: 1px solid #dee2e6; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px dashed #ccc; padding-top: 15px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .receipt-box { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

@php
    $booking = $booking ?? $payment->eventBooking ?? null;

    // Utilisation des vrais noms de colonnes validés
    $totalChambre = $booking ? ($booking->total_price ?? 0) : 0;
    $totalTheorique = $booking ? $booking->grand_total : ($payment->amount ?? 0);

    $prixUnitaire = $booking?->room?->roomType?->base_price ?? 0;
    $typeChambre = strtolower($booking?->room?->roomType?->name ?? '');

    if ($booking) {
        $debut = \Illuminate\Support\Carbon::parse($booking->check_in);
        $fin = \Illuminate\Support\Carbon::parse($booking->check_out);

        if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
            $unites = $prixUnitaire > 0 ? round($totalChambre / $prixUnitaire) : 1;
            $texteDuree = $unites . ' heure(s)';
            $labelUnite = 'Tarif Horaire';
        } else {
            $unites = max(1, $debut->diffInDays($fin));
            $texteDuree = $unites . ' nuit(s)';
            $labelUnite = 'Tarif par Nuitée';
        }
    } else {
        $unites = 1; $texteDuree = '1 séjour(s)'; $labelUnite = 'Tarif';
    }

    $bookingId = $booking?->id ?? $payment->event_booking_id ?? 0;
    $totalDejaPayeEnBdd = \App\Models\Payment::getSommePayeePourReservation($bookingId);
    $resteAPayer = max(0, $totalTheorique - $totalDejaPayeEnBdd);

    $methodes = ['cash' => 'Espèces / Cash', 'card' => 'Carte Bancaire', 'mobile_money' => 'Mobile Money', 'bank_transfer' => 'Virement Bancaire'];
    $modeReglement = $methodes[$payment->payment_method] ?? ucfirst($payment->payment_method);
    $datePaiement = \Illuminate\Support\Carbon::parse($payment->date_encaissement ?? $payment->created_at ?? now());
@endphp



<div class="receipt-box">
    <!-- Boutons utilitaires -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 16px; background: #198754; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">🖨️ Imprimer / Sauvegarder PDF</button>
        <a href="{{ url('/admin/payments') }}" style="display: inline-block; padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; font-weight: bold; margin-left: 5px;">⬅️ Retour à la Caisse</a>
    </div>

    <!-- En-tête -->
    <div class="header">
        <div>
            <div class="logo">COMPLEXE HÔTELIER</div>
            <p style="margin: 5px 0 0 0; color: #555;">Service Caisse & Comptabilité</p>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; text-transform: uppercase; letter-spacing: 1px;">Reçu de Paiement</h2>
            <p style="margin: 5px 0 0 0; font-weight: bold;">N° {{ $payment->receipt_number }}</p>
            <p style="margin: 2px 0 0 0;">Date : {{ $payment->paid_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Infos Clients -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0; color: #777; text-transform: uppercase; font-size: 11px;">Rattaché à</h4>
            <strong>Réservation N° {{ $booking->id }}</strong><br>
            Chambre : N° {{ $booking->room?->number ?? 'N/A' }} ({{ $booking->room?->roomType?->name ?? 'N/A' }})
        </div>
        <div style="text-align: right;">
            <h4 style="margin: 0 0 5px 0; color: #777; text-transform: uppercase; font-size: 11px;">Mode de Règlement</h4>
            <strong style="text-transform: uppercase; color: #0d6efd;">{{ $modeReglement }}</strong><br>
            Statut : <span style="color: #198754; font-weight: bold;">Encaissé</span>
        </div>
    </div>

    <!-- Tableau de prestation -->
        <table class="details-table">
            <thead>
                <tr>
                    <th>Désignation de la prestation</th>
                    <!-- Utilise l'intitulé dynamique (Tarif Horaire ou Tarif par Nuitée) -->
                    <th style="text-align: right;">{{ $labelUnite }}</th>
                    <th style="text-align: right;">Quantité / Durée</th>
                    <th style="text-align: right;">Montant Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Prestation 1 : L'hébergement de base (Chambre) -->
                <tr>
                    <td>Hébergement : Occupation de la Chambre N° {{ $booking?->room?->number ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($prixUnitaire, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ $texteDuree }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($booking?->prix_total ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>

                <!-- Prestations suivantes : Les consommations de restauration s'ajoutent toutes seules -->
                @if($booking && $booking->cateringItems->count() > 0)
                    @foreach($booking->cateringItems as $item)
                        <tr>
                            <td style="color: #555;">🍽️ Restauration : {{ $item->name ?? 'Consommation Restaurant' }} ({{ $item->created_at->format('d/m/Y') }})</td>
                            <!-- Remplacez $item->price par votre colonne de montant si différente -->
                            <td style="text-align: right; color: #555;">{{ number_format($item->price ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td style="text-align: right; color: #555;">1</td>
                            <td style="text-align: right; color: #555; font-weight: bold;">{{ number_format($item->price ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>

        </table>


    <!-- Bloc des calculs financiers cumulés à droite -->
    <div style="margin-top: 30px; float: right; width: 380px; font-size: 14px; line-height: 1.8;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #dee2e6; padding: 4px 0;">
            <span style="color: #666;">Coût total obligatoire du séjour :</span>
            <span style="font-weight: bold;">{{ number_format($totalTheorique, 0, ',', ' ') }} FCFA</span>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #dee2e6; padding: 4px 0; color: #198754;">
            <span>Montant versé sur ce reçu :</span>
            <span style="font-weight: bold;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #dee2e6; padding: 4px 0; color: #6c757d; font-style: italic;">
            <span>Total cumulé déjà réglé (historique) :</span>
            <span>{{ number_format($totalDejaPayeEnBdd, 0, ',', ' ') }} FCFA</span>
        </div>

        <!-- Bloc de conclusion du solde de la facture -->
        @if($resteAPayer > 0)
            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding: 8px 0; border-top: 2px solid #333; font-size: 16px; font-weight: 900; color: #fd7e14;">
                <span>Reste à payer (Solde dû) :</span>
                <span>{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</span>
            </div>
        @else
            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding: 8px 0; border-top: 2px solid #333; font-size: 16px; font-weight: 900; color: #198754; background: rgba(25, 135, 84, 0.05); padding-left: 5px;">
                <span>🎯 État de la facture :</span>
                <span>ENTIÈREMENT SOLDÉE</span>
            </div>
        @endif
    </div>

    <div style="clear: both;"></div>

    @if($payment->notes)
        <div style="margin-top: 30px; padding: 10px; background: #f8f9fa; border-left: 4px solid #0d6efd; font-style: italic;">
            <strong>Note de caisse :</strong> {{ $payment->notes }}
        </div>
    @endif

    <div class="footer">
        <p>Merci pour votre confiance. Passez un excellent séjour parmi nous.</p>
        <p style="font-size: 10px; color: #aaa; margin-top: 10px;">Document généré automatiquement par le système de gestion hôtelière.</p>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>

</body>
</html>
