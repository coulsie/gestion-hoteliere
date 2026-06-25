<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier - {{ $dateDebut }} au {{ $dateFin }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; padding: 40px; background: #fff; margin: 0; }
        .header { border-bottom: 3px solid #0f172a; padding-bottom: 15px; margin-bottom: 30px; }
        .hotel-title { font-size: 24px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin: 0; color: #0f172a; }
        .report-subtitle { font-size: 14px; color: #64748b; margin-top: 5px; font-weight: 600; }
        .period-badge { background: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-weight: bold; color: #0f172a; display: inline-block; margin-top: 10px; font-size: 13px; }

        /* Grille des Caisses Principales */
        .grid-caisses { display: table; width: 100%; table-layout: fixed; margin-bottom: 40px; }
        .caisse-card { display: table-cell; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; vertical-align: top; }
        .caisse-card.total { background: #0f172a; color: #fff; border: none; }
        .caisse-card:not(:last-child) { right: 15px; padding-right: 15px; }
        .caisse-label { font-size: 11px; text-transform: uppercase; font-weight: 800; color: #64748b; }
        .caisse-card.total .caisse-label { color: #94a3b8; }
        .caisse-amount { font-size: 20px; font-weight: 800; margin-top: 8px; }

        /* Tableau de Ventilation de l'Hébergement */
        .section-title { font-size: 14px; font-weight: 800; text-transform: uppercase; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px; }
        .table-ventilation { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-ventilation th { background: #f8fafc; text-align: left; padding: 12px; font-size: 12px; font-weight: 700; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .table-ventilation td { padding: 12px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .table-ventilation tr:last-child td { font-weight: bold; border-top: 2px solid #e2e8f0; color: #0f172a; }

        .footer-date { text-align: right; font-size: 11px; color: #94a3b8; margin-top: 60px; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    <!-- En-tête Officiel de l'Hôtel -->
    <div class="header">
        <h1 class="hotel-title">🏨 HÔTEL BEL HORIZON</h1>
        <div class="report-subtitle">RAPPORT COMPTABLE ET ÉTAT DES RECETTES PAR CAISSE</div>
        <div class="period-badge">Période du {{ \Illuminate\Support\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Illuminate\Support\Carbon::parse($dateFin)->format('d/m/Y') }}</div>
    </div>

    <!-- 1. Synthèse des Caisses Principales -->
    <div class="grid-caisses">
        <div class="caisse-card total">
            <span class="caisse-label">💰 Chiffre d'Affaires Global</span>
            <div class="caisse-amount">{{ number_format($data['totalGeneral'], 0, '.', ' ') }} FCFA</div>
        </div>
        <div style="width: 20px; display: table-cell;"></div>
        <div class="caisse-card">
            <span class="caisse-label">🍽️ Caisse Restaurant & Comptoir</span>
            <div class="caisse-amount" style="color: #ca8a04;">{{ number_format($data['restaurant'], 0, '.', ' ') }} FCFA</div>
        </div>
        <div style="width: 20px; display: table-cell;"></div>
        <div class="caisse-card">
            <span class="caisse-label">🏢 Caisse Location de Salles</span>
            <div class="caisse-amount" style="color: #0891b2;">{{ number_format($data['salle'], 0, '.', ' ') }} FCFA</div>
        </div>
    </div>

    <!-- 2. Ventilation Détallée de la Caisse Hôtel -->
    <div class="section-title">🏨 Ventilation Détaillée de la Caisse Hébergement</div>
    <table class="table-ventilation">
        <thead>
            <tr>
                <th>Type d'Hébergement / Catégorie de Chambre</th>
                <th style="text-align: right;">Recette Totale Période</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>🏨 Chambres Normales (Suivi Journalier)</td>
                <td style="text-align: right; font-weight: 600;">{{ number_format($data['chambreNormale'], 0, '.', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>⏳ Heures de Passage (Tarifs Horaires)</td>
                <td style="text-align: right; font-weight: 600; color: #d97706;">{{ number_format($data['passage'], 0, '.', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>👑 Suites Exécutives & Prestige</td>
                <td style="text-align: right; font-weight: 600; color: #7c3aed;">{{ number_format($data['suite'], 0, '.', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Sous-Total Recette Hébergement</strong></td>
                <td style="text-align: right; font-weight: bold; color: #0284c7;">{{ number_format($data['chambreNormale'] + $data['passage'] + $data['suite'], 0, '.', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-date">Document comptable certifié généré le {{ now()->format('d/m/Y à H:i') }} - Direction Générale</div>

    <!-- Déclencheur Automatique de l'impression papier -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>
