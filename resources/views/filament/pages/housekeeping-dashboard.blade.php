<x-filament-panels::page>
    <!-- Chargement sécurisé de Bootstrap 5.3.2 -->
    <link href="https://jsdelivr.net" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Styles correctifs pour forcer le quadrillage et l'effet cliquable -->
    <style>
        .custom-grid-table {
            border: 2px solid #343a40 !important;
        }
        .custom-grid-table th, .custom-grid-table td {
            border: 1px solid #6c757d !important; /* Force le quadrillage gris visible */
            padding: 15px !important;
        }
        /* Style pour rendre les cellules cliquables visuellement */
        .cellule-cliquable {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .cellule-cliquable:hover {
            background-color: #f1f3f5 !important;
            transform: scale(1.01);
            box-shadow: inset 0 0 0 2px #0d6efd;
        }
    </style>

    <div class="bootstrap-scope bg-white p-3 rounded-3 shadow-sm text-dark">
        <div class="table-responsive">
            <table class="table table-bordered align-middle m-0 text-center custom-grid-table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 15%;">Chambre N°</th>
                        <th scope="col" style="width: 35%;">Catégorie / Type</th>
                        <th scope="col" style="width: 50%;">État de Propreté (Cliquer pour changer)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->chambres as $chambre)
                        @php
                            $badgeClass = match($chambre->housekeeping_status) {
                                'propre' => 'badge bg-success text-white px-4 py-2 fs-6',
                                'sale' => 'badge bg-danger text-white px-4 py-2 fs-6',
                                'en_cours' => 'badge bg-warning text-dark px-4 py-2 fs-6',
                                'maintenance' => 'badge bg-secondary text-white px-4 py-2 fs-6',
                                default => 'badge bg-light text-dark px-4 py-2 fs-6'
                            };

                            $labelEtat = match($chambre->housekeeping_status) {
                                'propre' => '🧼 PROPRE / PRÊTE',
                                'sale' => '🍂 SALE (À nettoyer)',
                                'en_cours' => '🧹 MÉNAGE EN COURS',
                                'maintenance' => '🛠️ MAINTENANCE',
                                default => strtoupper($chambre->housekeeping_status)
                            };
                        @endphp
                        <tr>
                            <!-- 1. NUMÉRO -->
                            <td class="fw-bold fs-4 text-dark bg-light">
                                {{ $chambre->number }}
                            </td>

                            <!-- 2. CATÉGORIE -->
                            <td class="fw-medium text-secondary text-start ps-4">
                                {{ $chambre->roomType->name ?? 'Non spécifiée' }}
                            </td>

                            <!-- 3. CELLULE ENTIÈRE CLIQUABLE -->
                            <td class="cellule-cliquable"
                                wire:click="changerEtatChambreAction({{ $chambre->id }})"
                                title="Cliquer pour modifier le statut">
                                <div class="d-flex justify-content-between align-items-center px-3">
                                    <span class="{{ $badgeClass }}">
                                        {{ $labelEtat }}
                                    </span>
                                    <span class="text-muted small border rounded px-2 py-1 bg-light" style="font-size: 11px;">
                                        👆 Cliquer pour basculer
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
