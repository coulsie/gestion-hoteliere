<x-filament-panels::page>
    <!-- 📦 INTÉGRATION DE BOOTSTRAP 5.2.3 -->
    <link href="https://jsdelivr.net" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
        /* 🎨 RE-STYLISATION GRAPHIQUE COMPTABLE HOTELIÈRE */
        .pms-wrapper {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        }
        .pms-table-responsive {
            max-height: 72vh;
            overflow: auto;
            border: 3px solid #0f172a !important;
            border-radius: 8px;
        }
        .pms-table {
            table-layout: fixed;
            width: max-content;
            margin-bottom: 0 !important;
        }
        .col-chambre {
            width: 170px !important;
            min-width: 170px !important;
            max-width: 170px !important;
        }
        .col-jour {
            width: 55px !important;
            min-width: 55px !important;
            max-width: 55px !important;
        }
        .pms-table th, .pms-table td {
            border: 1px solid #cbd5e1 !important;
            padding: 4px !important;
            vertical-align: middle !important;
            text-align: center !important;
            height: 52px !important;
        }
        .sticky-chambre {
            position: sticky !important;
            left: 0 !important;
            background: linear-gradient(90deg, #1e293b 0%, #334155 100%) !important;
            color: #ffffff !important;
            z-index: 10 !important;
            border-right: 4px solid #0f172a !important;
            text-align: left !important;
            padding-left: 12px !important;
        }
        tr:nth-child(even) .sticky-chambre {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%) !important;
        }
        .sticky-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 11 !important;
        }
        .sticky-header-chambre {
            position: sticky !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 12 !important;
            background-color: #0f172a !important;
            color: #ffffff !important;
            border-right: 4px solid #0f172a !important;
            font-weight: 900;
        }
        .badge-pms {
            display: block !important;
            position: relative !important;
            z-index: 20 !important;
            width: 100%;
            height: 100%;
            font-size: 11px !important;
            font-weight: 800 !important;
            line-height: 1.3 !important;
            padding: 8px 4px !important;
            border-radius: 5px !important;
            text-decoration: none !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            cursor: pointer !important;
        }
        .badge-pms:hover {
            transform: scale(1.04);
            box-shadow: 0 4px 8px rgba(0,0,0,0.25);
            color: #fff !important;
        }
        .cell-libre {
            background-color: #f8fafc !important;
            height: 100%;
            width: 100%;
            border-radius: 2px;
            transition: background 0.15s ease;
        }
        tr:nth-child(even) .cell-libre {
            background-color: #ffffff !important;
        }
        .cell-libre:hover {
            background-color: #d1fae5 !important;
        }
    </style>

    <div class="pms-wrapper container-fluid px-0">

        <!-- 🎛️ NAVIGATION MOIS -->
        <div class="card p-3 mb-3 border-dark shadow-sm bg-white text-dark">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" wire:click="moisPrecedent" class="btn btn-dark fw-bold">
                            « Mois Précédent
                        </button>
                        <button type="button" wire:click="moisSuivant" class="btn btn-dark fw-bold">
                            Mois Suivant »
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <h3 class="fw-bolder text-uppercase m-0 border-bottom border-dark border-3 d-inline-block pb-1">
                        📅 {{ $moisActuelTexte }}
                    </h3>
                </div>
                <div class="col-md-4 d-flex justify-content-end align-items-center gap-3">
                    <div class="d-flex align-items-center gap-1"><span class="badge bg-light border border-dark" style="width:15px;height:15px;display:inline-block;"></span> <small class="fw-bold">Libre</small></div>
                    <div class="d-flex align-items-center gap-1"><span class="badge bg-primary" style="width:15px;height:15px;display:inline-block;"></span> <small class="fw-bold">Séjour (Nuitée)</small></div>
                    <div class="d-flex align-items-center gap-1"><span class="badge bg-warning text-dark" style="width:15px;height:15px;display:inline-block;"></span> <small class="fw-bold">Passage (Horaire)</small></div>
                </div>
            </div>
        </div>

        <!-- 📊 GRILLE PMS QUADRILLÉE -->
        <div class="pms-table-responsive shadow">
            <table class="table table-bordered pms-table m-0">
                <thead>
                    <tr class="sticky-header">
                        <th class="col-chambre sticky-header-chambre align-middle text-center bg-dark text-white">
                            <div class="fw-black text-uppercase tracking-wider" style="font-size: 12px; font-weight:900;">CHAMBRES</div>
                        </th>
                        @foreach($joursDuMois as $jour)
                            @php
                                $isToday = $jour->isToday();
                                $isWeekend = $jour->isWeekend();
                                $bgHeaderClass = $isToday ? 'bg-danger text-white' : ($isWeekend ? 'bg-secondary text-white' : 'bg-dark text-white');
                            @endphp
                            <th class="col-jour text-center p-1 align-middle {{ $bgHeaderClass }}">
                                <div class="text-uppercase tracking-normal text-white-50" style="font-size: 9px;">{{ $jour->translatedFormat('D') }}</div>
                                <div class="fs-5 fw-extrabold lh-1 mt-1">{{ $jour->format('d') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($grilleOccupation as $ligne)
                        <tr>
                            <td class="col-chambre sticky-chambre align-middle">
                                <span class="d-block fw-extrabold text-white fs-5">Ch. {{ $ligne['chambre_number'] }}</span>
                                <small class="text-white-50 text-uppercase fw-bold d-block truncate" style="font-size: 10px; max-width: 145px;">{{ $ligne['chambre_type'] }}</small>
                            </td>

                            @foreach($joursDuMois as $jour)
                                @php
                                    $cellule = $ligne['planning'][$jour->format('Y-m-d')];
                                @endphp
                                <td class="col-jour p-1">
                                    @if($cellule)
                                        @php
                                            $urlEdit = $cellule['url'] ?? '#';
                                            $bootstrapClass = $cellule['type'] === 'passage' ? 'bg-warning text-dark' : 'bg-primary text-white';
                                        @endphp

                                        @if($cellule['cliquable'])
                                            <!-- Version Admin : Bouton cliquable direct -->
                                            <!-- 🛠️ FIX : Retrait de wire:navigate pour laisser Filament exécuter la modale au chargement -->
                                            <a href="{{ $urlEdit }}" class="badge-pms {{ $bootstrapClass }}" title="Client : {{ $cellule['client'] }} (Cliquez pour modifier)">
                                                👤 {{ $cellule['client'] }}
                                            </a>
                                        @else
                                            <!-- Version Réceptionniste : Badge verrouillé anti-fraude -->
                                            <div class="badge-pms {{ $bootstrapClass }} opacity-75" style="cursor: not-allowed;" title="Client : {{ $cellule['client'] }} (Modification Bloquée)">
                                                🔒 {{ $cellule['client'] }}
                                            </div>
                                        @endif

                                    @else
                                        <div class="cell-libre" title="Chambre disponible à la réservation"></div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-filament-panels::page>
