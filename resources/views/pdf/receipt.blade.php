@php
    // 1. DÉTECTION DE LA CAISSE SÉCURISÉE
    $numRecu = $payment->receipt_number ?? '';
    $estRestaurant = ($payment->payment_type === 'restauration' || str_starts_with($numRecu, 'REC-RESTO-'));
    $estSalle = ($payment->payment_type === 'salle' || str_starts_with($numRecu, 'REC-SALLE-'));

    // 2. RÉCUPÉRATION DU DOSSIER CLIENT
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

    // 4. FIX HISTORIQUE CUMULÉ : On additionne tous les reçus de salle pour cet événement
    $bookingId = $booking?->id ?? $payment->event_booking_id ?? 0;
    if ($estRestaurant) {
        $totalDejaPayeEnBdd = (float) $payment->amount;
        $resteAPayer = 0;
    } elseif ($estSalle) {
        // SQL DIRECT : Calcule la somme brute de TOUTES les tranches payées par la SIFCA
        $totalDejaPayeEnBdd = (float) \Illuminate\Support\Facades\DB::table('payments')
            ->where('payment_type', 'salle')
            ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%')
            ->sum('amount');

        // Le reste à payer est la soustraction arithmétique exacte
        $resteAPayer = max(0, $totalTheorique - $totalDejaPayeEnBdd);
    } else {
        // Hôtel
        $totalDejaPayeEnBdd = \App\Models\Payment::getSommePayeePourReservation($bookingId);
        $resteAPayer = max(0, $totalTheorique - $totalDejaPayeEnBdd);
    }

    // FIX TRADUCTION : Intégration complète et propre des passerelles de paiements mobiles et Wave
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

    <!-- CLIENT -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px; font-size: 14px;">
        <div>
            <span style="color:#777; font-size:11px; text-transform:uppercase; display:block;">Client</span>
            @if($estRestaurant)
                <strong>🍽️ Client Resto / Comptoir</strong>
            @elseif($estSalle)
                <strong>🏢 Client : {{ $booking?->client_name ?? 'Organisation N/A' }}</strong><br>
                <small>Salle : {{ $booking?->eventSpace?->name ?? 'N/A' }}</small>
            @else
                <strong>🏨 Client : {{ $booking?->customer_name ?? 'Nom N/A' }}</strong><br>
                <small>Chambre N° {{ $booking?->room?->number ?? 'N/A' }}</small>
            @endif
        </div>
        <div style="text-align:right;">
            <span style="color:#777; font-size:11px; text-transform:uppercase; display:block;">Règlement</span>
            <span>Mode : <strong>{{ $modeReglement }}</strong></span>
        </div>
    </div>

    <!-- GRILLE -->
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align:right; width:120px;">Prix Unitaire</th>
                <th style="text-align:center; width:60px;">Qté</th>
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
                <tr>
                    <td>{{ $estSalle ? '🏢 Location d\'Espace' : '🏨 Occupation de Chambre' }}</td>
                    <td style="text-align:right;">{{ number_format($totalTheorique, 0, ',', ' ') }} F CFA</td>
                    <td style="text-align:center;">1</td>
                    <td style="text-align:right; font-weight:bold;">{{ number_format($totalTheorique, 0, ',', ' ') }} F CFA</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- TOTAL -->
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
                <span style="color:#666;">Coût total de la prestation :</span>
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
                <span>{{ $resteAPayer > 0 ? 'Reste à payer (Solde dû) :' : 'État de la facture :' }}</span>
                <span>{{ $resteAPayer > 0 ? number_format($resteAPayer, 0, ',', ' ') . ' FCFA' : 'ENTIÈREMENT SOLDÉE' }}</span>
            </div>
        @endif
    </div>

    <div style="clear:both;"></div>
</body>
</html>
