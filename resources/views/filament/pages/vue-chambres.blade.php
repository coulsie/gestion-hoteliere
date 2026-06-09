<x-filament-panels::page class="max-w-full w-full">

    <!-- Injection de Styles CSS Ultra-Éclatants -->
    <style>
        .hotel-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        /* Effet de lévitation et amplification de la lueur néon au survol */
        .hotel-card:hover {
            transform: translateY(-10px) scale(1.03);
        }
        .neon-shadow-emerald {
            box-shadow: 0 15px 30px -5px rgba(5, 245, 165, 0.45), 0 10px 15px -6px rgba(5, 245, 165, 0.45);
        }
        .neon-shadow-emerald:hover {
            box-shadow: 0 25px 45px -5px rgba(5, 245, 165, 0.7), 0 15px 20px -6px rgba(5, 245, 165, 0.7);
        }
        .neon-shadow-red {
            box-shadow: 0 15px 30px -5px rgba(255, 46, 99, 0.45), 0 10px 15px -6px rgba(255, 46, 99, 0.45);
        }
        .neon-shadow-red:hover {
            box-shadow: 0 25px 45px -5px rgba(255, 46, 99, 0.7), 0 15px 20px -6px rgba(255, 46, 99, 0.7);
        }
        .neon-shadow-purple {
            box-shadow: 0 15px 30px -5px rgba(186, 12, 247, 0.45), 0 10px 15px -6px rgba(186, 12, 247, 0.45);
        }
        .neon-shadow-purple:hover {
            box-shadow: 0 25px 45px -5px rgba(186, 12, 247, 0.7), 0 15px 20px -6px rgba(186, 12, 247, 0.7);
        }
        /* Animation fluide pour l'indicateur de surveillance Live */
        @keyframes pulse-neon {
            0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 12px #05f5a5; }
            50% { transform: scale(1.2); opacity: 0.6; box-shadow: 0 0 4px #05f5a5; }
        }
        .pulse-element {
            animation: pulse-neon 1.5s infinite ease-in-out;
        }
    </style>

    <!-- Barre d'outils du haut : Filtres + Indicateur Live-Script -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-gray-200 dark:border-white/10 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mr-2">Filtrer par :</span>

            <x-filament::button wire:click="setFilter('all')" color="{{ $statusFilter === 'all' ? 'primary' : 'gray' }}" size="sm" icon="heroicon-m-funnel" class="font-bold shadow-sm">
                Toutes les chambres
            </x-filament::button>

            <x-filament::button wire:click="setFilter('disponible')" color="success" size="sm" icon="heroicon-m-check-circle" class="font-bold {{ $statusFilter === 'disponible' ? 'ring-2 ring-emerald-400 brightness-110' : 'opacity-60 hover:opacity-100' }}">
                Disponibles
            </x-filament::button>

            <x-filament::button wire:click="setFilter('occupee')" color="danger" size="sm" icon="heroicon-m-user" class="font-bold {{ $statusFilter === 'occupee' ? 'ring-2 ring-rose-400 brightness-110' : 'opacity-60 hover:opacity-100' }}">
                Occupées (Calendrier)
            </x-filament::button>

            <x-filament::button wire:click="setFilter('menage')" color="warning" size="sm" icon="heroicon-m-sparkles" class="font-bold {{ $statusFilter === 'menage' ? 'ring-2 ring-amber-400 brightness-110' : 'opacity-60 hover:opacity-100' }}">
                En ménage
            </x-filament::button>
        </div>

        <!-- Indicateur Visuel Haute Visibilité -->
        <div class="flex items-center gap-2.5 px-4 py-2 rounded-full bg-emerald-500/10 dark:bg-emerald-500/5 border border-emerald-500/30">
            <span class="h-2.5 w-2.5 rounded-full bg-[#05f5a5] pulse-element"></span>
            <span class="text-xs font-black text-emerald-600 dark:text-[#05f5a5] uppercase tracking-wider">Surveillance Live Active</span>
        </div>
    </div>

    <!-- Conteneur Flexbox horizontal pleine largeur -->
    <div style="display: flex; flex-wrap: wrap; gap: 24px; width: 100%;">
        @forelse($chambres as $chambre)
            @php
                [$cardBg, $shadowClass, $textColor, $subTextColor, $badgeStyle, $icon, $statusLabel] = match($chambre->statut_calculer) {
                    // Vert Électrique / Menthe Éclatante
                    'disponible' => [
                        'bg-gradient-to-br from-[#05f5a5] to-[#00b4d8]', 'neon-shadow-emerald',
                        'text-slate-950', 'text-slate-900 font-bold',
                        'bg-slate-950/10 text-slate-950 border-slate-950/20', 'heroicon-o-check-circle', 'Disponible'
                    ],
                    // Rouge Flash / Rose Néon Intense
                    'occupee' => [
                        'bg-gradient-to-br from-[#ff2e93] to-[#ff8e53]', 'neon-shadow-red',
                        'text-white', 'text-rose-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-user', 'Occupée'
                    ],
                    // Violet Électrique / Orange Éclatant
                    'menage' => [
                        'bg-gradient-to-br from-[#ba0cf7] to-[#ff6b6b]', 'neon-shadow-purple',
                        'text-white', 'text-purple-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-sparkles', 'En Ménage'
                    ],
                    default => [
                        'bg-gradient-to-br from-slate-600 to-slate-800', 'shadow-lg',
                        'text-white', 'text-slate-200',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-no-symbol', 'Inconnu'
                    ]
                };
            @endphp

            <!-- Case de la chambre stylisée avec dégradé vif, ombres néon et animations -->
            <div class="hotel-card {{ $cardBg }} {{ $shadowClass }} p-6 rounded-3xl flex flex-col justify-between"
                 style="flex: 1 1 260px; max-w: 320px; min-height: 220px;">

                <div>
                     <!-- En-tête de case : Style Hôtel Premium & Éclat Visuel -->
                    <div class="flex items-center justify-between gap-2 pb-3 mb-2 border-b border-white/20">

                        <!-- Numéro de la chambre avec ombre portée et lettrage compact -->
                        <div class="flex items-baseline gap-1">
                            <span class="text-xs font-black uppercase tracking-widest opacity-60 {{ $textColor }}">Chambre</span>
                            <h3 class="text-3xl font-black {{ $textColor }} tracking-tighter drop-shadow-sm">
                                {{ $chambre->number }}
                            </h3>
                        </div>

                        <!-- Badge d'état dynamique à haute visibilité et effet miroir -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-full border shadow-sm backdrop-blur-xl transition-all duration-300 hover:scale-105 {{ $badgeStyle }}">
                            <span class="relative flex h-2 w-2">
                                <!-- Point lumineux clignotant intégré au badge pour l'effet "Live" -->
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-current opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
                            </span>
                            <x-filament::icon :icon="$icon" class="h-3.5 w-3.5 stroke-[3]" />
                            {{ $statusLabel }}
                        </span>

                    </div>

                    <!-- Catégorie de chambre -->
                    <div class="mt-5 flex items-center gap-2 {{ $subTextColor }}">
                        <x-filament::icon icon="heroicon-o-home" class="h-4 w-4 opacity-90" />
                        <p class="text-xs font-black tracking-widest uppercase">
                            {{ $chambre->roomType->name }}
                        </p>
                    </div>

                    <!-- Bloc détails du planning -->
                    @if($chambre->statut_calculer === 'occupee')
                        <div class="mt-4 p-3 rounded-2xl bg-black/40 text-white text-xs space-y-1.5 border border-white/10 shadow-inner backdrop-blur-md">
                            <div class="truncate flex items-center gap-1">👤 Client : <span class="font-bold text-white">{{ $chambre->client_actuel }}</span></div>
                            <div class="mt-1 flex items-center gap-1">📅 Départ : <span class="font-black text-[#05f5a5]">{{ $chambre->date_depart }}</span></div>
                        </div>
                    @endif
                </div>

                <!-- Pied de case : Bouton d'action épuré et arrondi -->
                <div class="mt-5 pt-3 border-t border-current/10 flex justify-end">
                    <x-filament::button
                        href="{{ url('/admin/bookings') }}"
                        tag="a"
                        color="white"
                        size="sm"
                        icon="heroicon-m-cog-6-tooth"
                        icon-position="before"
                        class="font-black text-slate-950 bg-white hover:bg-slate-100 shadow-md rounded-xl border-transparent transition-all"
                    >
                        Gérer
                    </x-filament::button>
                </div>

            </div>
        @empty
            <div class="w-full text-center py-16 text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-white/5 rounded-2xl border border-dashed border-gray-300 dark:border-white/15">
                <x-filament::icon icon="heroicon-o-no-symbol" class="h-10 w-10 mx-auto mb-3 text-gray-400" />
                <p class="text-sm font-bold uppercase tracking-wider">Aucune chambre ne correspond à ce critère actuellement.</p>
            </div>
        @endforelse
    </div>

</x-filament-panels::page>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Configure l'intervalle de rafraîchissement (10000 ms = 10 secondes)
        const INTERVALLE_RAFRAICHISSEMENT = 10000;

        setInterval(() => {
            // Vérifie si le composant Livewire Filament est disponible sur la page
            const composantLivewire = window.Livewire;

            if (composantLivewire) {
                // Déclenche l'action de mise à jour native de Livewire
                composantLivewire.dispatch('$refresh');
                console.log('🔄 Statuts des chambres actualisés en direct.');
            }
        }, INTERVALLE_RAFRAICHISSEMENT);
    });
</script>
