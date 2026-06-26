@php
    // 1. DÉTECTION RIGOUREUSE DES TROIS CAISSES DISTINCTES
    $numRecu = $payment->receipt_number ?? '';

    // Une recette est une chambre SI marquée en BDD ou si le reçu commence par REC- (sans RESTO ni SALLE)
    $estRestaurant = ($payment->payment_type === 'restauration' || str_starts_with($numRecu, 'REC-RESTO-'));
    $estSalle = ($payment->payment_type === 'salle' || str_starts_with($numRecu, 'REC-SALLE-'));
    $estChambre = ($payment->payment_type === 'chambre' || (! $estRestaurant && ! $estSalle));

    // 2. RÉCUPÉRATION DU DOSSIER CLIENT (Avec reconnexions de sécurité)
    $booking = $booking ?? $payment->eventBooking ?? null;
    if (!$booking && $payment->event_booking_id) {
        $booking = \App\Models\EventBooking::with('eventSpace')->find($payment->event_booking_id);
    }

    // 3. COÛT TOTAL CONTRACTUEL
    if ($estRestaurant) {
        $totalTheorique = (float) $payment->amount;
    } elseif ($estSalle) {
        $totalTheorique = $booking ? (float)($booking->total_amount ?? 1500000) : 1500000;
    } else {
        $totalTheorique = $booking ? (float)($booking->grand_total ?? $booking->total_price ?? 0) : (float)$payment->amount;
    }

    // 4. FIX HISTORIQUE CUMULÉ CLOISONNÉ (Isolation stricte par event_booking_id)
    $bookingId = $booking?->id ?? $payment->event_booking_id ?? 0;

    if ($estRestaurant) {
        $totalDejaPayeEnBdd = (float) $payment->amount;
        $resteAPayer = 0;
    } elseif ($estSalle) {
        // FIX PARENTHÈSES SQL : Calcule uniquement le cumul des reçus de CET ÉVÉNEMENT PRÉCIS
        $totalDejaPayeEnBdd = (float) \Illuminate\Support\Facades\DB::table('payments')
            ->where('event_booking_id', $bookingId)
            ->where(function ($query) {
                $query->where('payment_type', 'salle')
                      ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%');
            })
            ->sum('amount');

        // Sécurité si la BDD est en retard sur le cumul
        if ($totalDejaPayeEnBdd <= 0) {
            $totalDejaPayeEnBdd = (float) $payment->amount;
        }

        $resteAPayer = max(0, $totalTheorique - $totalDejaPayeEnBdd);
    } else {
        // Hôtel / Chambres
        $totalDejaPayeEnBdd = \App\Models\Payment::getSommePayeePourReservation($bookingId);
        $resteAPayer = max(0, $totalTheorique - $totalDejaPayeEnBdd);
    }

    $methodes = [
        'cash'          => 'Espèces / Cash',
        'wave'          => 'Wave',
        'orange_money'  => 'Orange Money',
        'mtn_momo'      => 'MTN Mobile Money',
        'moov_money'    => 'Moov Money',
        'card'          => 'Carte Bancaire',
        'bank_transfer' => 'Virement Bancaire'
    ];
    $modeReglement = $methodes[$payment->payment_method] ?? ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'Espèces'));
    $datePaiement = \Illuminate\Support\Carbon::parse($payment->date_encaissement ?? $payment->created_at ?? now());
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; padding: 20px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; font-size: 13px; }
        th { text-align: left; padding: 8px; background: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        td { padding: 8px; border-bottom: 1px solid #eee; }

        /* Masquage intelligent du bouton d'assistance sur la version papier imprimée */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <!-- ENTÊTE -->
    <div style="display: flex; justify-content: space-between; border-bottom: 3px solid #10b981; padding-bottom: 10px;">
        <div>
            <h2 style="margin:0; color:#10b981; text-transform:uppercase;">COMPLEXE HÔTELIER</h2>
            <small style="color:#666;">Service Caisse & Comptabilité</small>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">Reçu de Paiement</h3>
            <strong>N° {{ $payment->receipt_number }}</strong><br>
            <small style="color:#777;">Date : {{ $datePaiement->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <!-- CLIENT (SÉCURISÉ) -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px; font-size: 14px;">
        <div>
            <span style="color:#777; font-size:11px; text-transform:uppercase; display:block;">Bénéficiaire / Client</span>
            @if($estRestaurant)
                <strong>🍽️ Client Resto de Passage</strong>
            @elseif($estSalle)
                <strong>🏢 Client : {{ $booking?->client_name ?? 'Organisation N/A' }}</strong><br>
                <small>Salle : {{ $booking?->eventSpace?->name ?? 'N/A' }}</small>
            @else
                <!-- FIX ACQUISITION CLIENT : Lecture croisée pour extraire le vrai résident de l'hôtel -->
                <strong>🏨 Client : {{ $booking?->customer_name ?? $payment->booking?->customer_name ?? 'Client Hôtel' }}</strong><br>
                <small>Chambre N° {{ $booking?->room?->number ?? $payment->booking?->room?->number ?? 'N/A' }}</small>
            @endif
        </div>
        <div style="text-align:right;">
            <span style="color:#777; font-size:11px; text-transform:uppercase; display:block;">Règlement</span>
            <span>Mode : <strong>{{ $modeReglement }}</strong></span>
        </div>
    </div>

   <!-- GRILLE DE PRESTATIONS -->
<!-- GRILLE DE PRESTATIONS -->
<table>
    <thead>
        <tr>
            <th>Désignation</th>
            <th style="text-align:right; width:120px;">Prix Unitaire</th>

            <!-- DYNAMIQUE : Alterne entre "Nuits", "Heures" ou "Qté" selon le type de prestation -->
            <th style="text-align:center; width:80px;">
                @if($estRestaurant)
                    Qté
                @elseif(isset($booking->room->roomType) && str_contains(strtolower($booking->room->roomType->name ?? ''), 'passage'))
                    Heures
                @else
                    Nuits
                @endif
            </th>

            <th style="text-align:right; width:120px;">Montant Net</th>
        </tr>
    </thead>
    <tbody>
        @if($estRestaurant)
            @php
                $commande = \App\Models\CateringOrder::where('total_amount', $payment->amount)->orderBy('created_at', 'desc')->first();
                $lignes = $commande ? $commande->items : [];
            @endphp
            @forelse($lignes as $item)
                <tr>
                    <td>🍽️ {{ $item->cateringItem?->name ?? 'Consommation' }}</td>
                    <td style="text-align:right;">{{ number_format((float)$item->price, 0, ',', ' ') }} F CFA</td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right; font-weight:bold;">{{ number_format(((float)$item->price * (int)$item->quantity), 0, ',', ' ') }} F CFA</td>
                </tr>
            @empty
                <tr>
                    <td>🍽️ Consommations directes Restaurant</td>
                    <td style="text-align:right;">{{ number_format($payment->amount, 0, ',', ' ') }} F CFA</td>
                    <td style="text-align:center;">1</td>
                    <td style="text-align:right; font-weight:bold;">{{ number_format($payment->amount, 0, ',', ' ') }} F CFA</td>
                </tr>
            @endforelse
        @else
            @if(!$estSalle && isset($booking->room->roomType))
                @php
                    $prixUnitaireChambre = (float) $booking->room->roomType->base_price;
                    $nomType = strtolower($booking->room->roomType->name ?? '');

                    // CORRECTION : Détection fiable de la quantité (Heures ou Nuits)
                    if (str_contains($nomType, 'passage')) {
                        // Extrait le nombre d'heures directement de l'écart réel entre l'arrivée et le départ
                        $debut = \Carbon\Carbon::parse($booking->check_in);
                        $fin = \Carbon\Carbon::parse($booking->check_out);
                        $qteCalculable = (int) $debut->diffInHours($fin);

                        // Sécurité si l'écart d'heures est arrondi à 0 par Carbon
                        $qteCalculable = $qteCalculable <= 0 ? (int)($booking->nombre_heures ?? 1) : $qteCalculable;
                    } else {
                        // Séjour classique : Nombre de nuits
                        $dateArrivee = \Carbon\Carbon::parse($booking->check_in)->floorDay();
                        $dateSortie = \Carbon\Carbon::parse($booking->check_out)->floorDay();
                        $qteCalculable = (int) $dateArrivee->diffInDays($dateSortie);
                        $qteCalculable = $qteCalculable <= 0 ? 1 : $qteCalculable;
                    }
                @endphp
                <tr>
                    <td>🏨 Hébergement : Chambre N° {{ $booking->room->number ?? '' }} ({{ $booking->room->roomType->name ?? '' }})</td>
                    <td style="text-align:right;">{{ number_format($prixUnitaireChambre, 0, ',', ' ') }} F CFA</td>

                    <!-- REFUGE DE LA QUANTITÉ CORRIGÉE (Affiche 4 pour 4 heures de passage) -->
                    <td style="text-align:center;">{{ $qteCalculable }}</td>

                    <td style="text-align:right; font-weight:bold;">{{ number_format($prixUnitaireChambre * $qteCalculable, 0, ',', ' ') }} F CFA</td>
                </tr>
            @else
                <tr>
                    <td>🏢 {{ $estSalle ? 'Location d\'Espace Événementiel' : 'Hébergement : Occupation de Chambre' }}</td>
                    <td style="text-align:right;">{{ number_format($totalTheorique, 0, ',', ' ') }} F CFA</td>
                    <td style="text-align:center;">1</td>
                    <td style="text-align:right; font-weight:bold;">{{ number_format($totalTheorique, 0, ',', ' ') }} F CFA</td>
                </tr>
            @endif
        @endif
    </tbody>
</table>

    <!-- TOTAL FINANCIER -->
    <div style="margin-top:20px; float:right; width:380px; font-size:14px; line-height:1.8;">
        @if($estRestaurant)
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding:3px 0;">
                <span>Sous-Total Net HT :</span>
                <span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:5px; padding:5px 0; border-top:2px solid #333; font-size:16px; font-weight:bold; color:#10b981;">
                <span>TOTAL PAYÉ :</span>
                <span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
            </div>
        @else
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #dee2e6; padding:4px 0;">
                <span>Coût total de la prestation :</span>
                <span style="font-weight:bold;">{{ number_format($totalTheorique, 0, ',', ' ') }} FCFA</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #dee2e6; padding:4px 0; color:#198754;">
                <span>Montant versé sur ce reçu :</span>
                <span style="font-weight:bold;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #dee2e6; padding:4px 0; color:#6c757d; font-style:italic;">
                <span>Total cumulé déjà réglé (historique) :</span>
                <span style="font-weight:bold;">{{ number_format($totalDejaPayeEnBdd, 0, ',', ' ') }} FCFA</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:10px; padding:8px 0; border-top:2px solid #333; font-size:16px; font-weight:bold; color:{{ $resteAPayer > 0 ? '#fd7e14' : '#198754' }};">
            {{-- Libellé et Montant du Reste à Payer --}}
            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding: 8px 0; border-top: 2px solid #333; font-size: 16px; font-weight: bold; color: {{ $resteAPayer > 0 ? '#fd7e14' : '#198754' }};">
                <span>{{ $resteAPayer > 0 ? 'Reste à payer (Solde dû) :' : 'État de la facture :' }}</span>
                <span>{{ $resteAPayer > 0 ? number_format($resteAPayer, 0, ',', ' ') . ' FCFA' : 'ENTIÈREMENT SOLDÉE' }}</span>
            </div>
        @endif
    </div>

    {{-- Nettoyage des alignements flottants --}}
    <div style="clear: both;"></div>

    {{-- ZONE DE SIGNATURES COMPTABLES --}}
    <div style="display: flex; justify-content: space-between; margin-top: 50px; font-size: 12px; text-align: center;">
        <div>
            <p style="margin-bottom: 60px; color: #555; font-weight: bold; text-transform: uppercase;">Signature du Client</p>
            <hr style="width: 160px; border: 0; border-top: 1px dashed #bbb;">
        </div>
        <div>
            <p style="margin-bottom: 60px; color: #555; font-weight: bold; text-transform: uppercase;">Le Caissier / La Réception</p>
            <hr style="width: 160px; border: 0; border-top: 1px dashed #bbb;">
        </div>
    </div>

    {{-- BOUTON DE RELANCE (Visible uniquement à l'écran, masqué à l'impression) --}}
    <div class="no-print" style="margin-top: 60px; text-align: center;">
        <button onclick="window.print();" style="padding: 12px 24px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 4px 6px rgba(0,0,0,0.08);">
            🖨️ Relancer l'Impression Matérielle
        </button>
    </div>

    {{-- INTERCEPTOR JAVASCRIPT : Déclenchement automatique des pilotes d'impression --}}
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>

</body>
</html>
