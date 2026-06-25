<x-filament-panels::page>
    <!-- 📅 RENDU DES CALENDRIERS CORRIGÉ ET STABILISÉ V5 -->
    <form>
        {{ $this->form }}
    </form>

    @php $data = $this->recettes; @endphp

    <!-- Reste de vos superbes cartes HTML (Chiffre d'Affaires Global, Caisse Restaurant, Caisse Salles, etc.) -->



    <!-- 🎨 CARTES FINANCIÈRES ÉCLATANTES STYLE BOOTSTRAP / NEON -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 25px;">

        <!-- Total Général -->
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 20px; border-radius: 12px; border-left: 6px solid #f43f5e; box-shadow: 0 4px 6px rgba(0,0,0,0.05); color: #fff;">
            <small style="text-transform: uppercase; font-weight: 800; color: #fda4af;">💰 Chiffre d'Affaires Global</small>
            <h2 style="font-size: 24px; font-weight: 900; margin-top: 5px;">{{ number_format($data['totalGeneral'], 0, '.', ' ') }} FCFA</h2>
        </div>

        <!-- Caisse Restaurant -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 6px solid #eab308; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <small style="text-transform: uppercase; font-weight: 800; color: #ca8a04;">🍽️ Caisse Restaurant</small>
            <h2 style="font-size: 24px; font-weight: 900; margin-top: 5px; color: #1e293b;">{{ number_format($data['restaurant'], 0, '.', ' ') }} FCFA</h2>
        </div>

        <!-- Caisse Salles -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 6px solid #06b6d4; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <small style="text-transform: uppercase; font-weight: 800; color: #0891b2;">🏢 Caisse Salles</small>
            <h2 style="font-size: 24px; font-weight: 900; margin-top: 5px; color: #1e293b;">{{ number_format($data['salle'], 0, '.', ' ') }} FCFA</h2>
        </div>
    </div>

    <!-- 🏨 SÉCTION EXTENSIVE : VENTILATION DE L'HÉBERGEMENT -->
    <div style="background: #fff; padding: 25px; border-radius: 12px; margin-top: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h3 style="font-size: 16px; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">🏨 Détail de la Caisse Hébergement / Hôtel</h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <span style="color: #64748b; font-size: 13px; font-weight: 600;">🛏️ Chambres Normales :</span>
                <p style="font-size: 18px; font-weight: 800; color: #1e293b; margin-top: 4px;">{{ number_format($data['chambreNormale'], 0, '.', ' ') }} FCFA</p>
            </div>
            <div>
                <span style="color: #64748b; font-size: 13px; font-weight: 600;">⏳ Heures de Passage :</span>
                <p style="font-size: 18px; font-weight: 800; color: #d97706; margin-top: 4px;">{{ number_format($data['passage'], 0, '.', ' ') }} FCFA</p>
            </div>
            <div>
                <span style="color: #64748b; font-size: 13px; font-weight: 600;">👑 Suites Exécutives :</span>
                <p style="font-size: 18px; font-weight: 800; color: #7c3aed; margin-top: 4px;">{{ number_format($data['suite'], 0, '.', ' ') }} FCFA</p>
            </div>
        </div>
    </div>

    <!-- 🌐 ÉCOUTEUR LIVEWIRE POUR OUVERTURE D'ONGLET AUTOMATIQUE -->
<script>
    window.addEventListener('open-window', event => {
        window.open(event.detail.url, '_blank');
    });
</script>

</x-filament-panels::page>
