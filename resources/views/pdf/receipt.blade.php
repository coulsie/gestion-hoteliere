<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement - {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 40px; font-size: 14px; }
        .receipt-box { max-w: 800px; margin: auto; border: 1px solid #eee; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); }
        .header { display: flex; justify-content: space-between; border-b: 2px solid #333; padding-b: 20px; mb: 20px; }
        .logo { font-size: 24px; font-weight: bold; uppercase; color: #0d6efd; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .details-table th { background: #f8f9fa; text-align: left; padding: 10px; border-bottom: 2px solid #dee2e6; font-weight: bold; }
        .details-table td { padding: 12px 10px; border-bottom: 1px solid #dee2e6; }
        .total-row { font-size: 18px; font-weight: bold; text-align: right; margin-top: 30px; padding-top: 15px; border-top: 2px solid #333; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px dashed #ccc; padding-top: 15px; }

        /* Masque les boutons d'action lors de l'impression physique ou PDF */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .receipt-box { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="receipt-box">
    <!-- Boutons utilitaires du haut -->
    <!-- Boutons utilitaires du haut -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 16px; background: #198754; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">🖨️ Imprimer / Sauvegarder PDF</button>

        <!-- Correction : Redirige proprement le caissier vers l'index de la comptabilité -->
        <a href="{{ url('/admin/payments') }}" style="display: inline-block; padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; font-weight: bold; margin-left: 5px;">⬅️ Retour à la Caisse</a>
    </div>


    <!-- En-tête du Reçu -->
    <div class="header">
        <div>
            <div class="logo">COMPLEXE HÔTELIER</div>
            <p style="margin: 5px 0 0 0; color: #555;">Service Caisse & Comptabilité</p>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; uppercase; letter-spacing: 1px;">Reçu de Paiement</h2>
            <p style="margin: 5px 0 0 0; font-weight: bold;">N° {{ $payment->receipt_number }}</p>
            <p style="margin: 2px 0 0 0;">Date : {{ $payment->paid_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Informations Client & Réservation -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0; color: #777; uppercase; font-size: 11px;">Rattaché à</h4>
            <strong>Réservation N° {{ $booking->id }}</strong><br>
            Chambre : N° {{ $booking->room?->number ?? 'N/A' }} ({{ $booking->room?->roomType?->name ?? 'N/A' }})
        </div>
        <div style="text-align: right;">
            <h4 style="margin: 0 0 5px 0; color: #777; uppercase; font-size: 11px;">Mode de Règlement</h4>
            <strong style="text-transform: uppercase;">
                {{ str_replace('_', ' ', $payment->payment_method) }}
            </strong><br>
            Statut : <span style="color: #198754; font-weight: bold;">Encaissé</span>
        </div>
    </div>

    <!-- Tableau de Désignation Financière -->
    <table class="details-table">
        <thead>
            <tr>
                <th>Désignation de la prestation</th>
                <th style="text-align: right;">Montant Partiel</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Règlement/Acompte lié à l'occupation de la Chambre N° {{ $booking->room?->number ?? 'N/A' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <!-- Montant Total Écrit en Gros -->
    <div class="total-row">
        Montant Total Encaissé : {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
    </div>

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

<!-- Script d'automatisation de l'impression navigateur -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Lance automatiquement la boîte d'impression (ou l'enregistrement PDF) 500ms après le chargement
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>

</body>
</html>
