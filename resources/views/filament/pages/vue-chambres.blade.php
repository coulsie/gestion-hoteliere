<x-filament-panels::page class="max-w-full w-full">

    <!-- Injection de Styles CSS Personnalisés -->
    <style>
        .hotel-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        /* Effet de zoom et augmentation de l'ombre néon au survol */
        .hotel-card:hover {
            transform: translateY(-8px) scale(1.02);
        }
        .neon-shadow-emerald {
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4), 0 8px 10px -6px rgba(16, 185, 129, 0.4);
        }
        .neon-shadow-emerald:hover {
            box-shadow: 0 20px 35px -5px rgba(16, 185, 129, 0.6), 0 12px 15px -6px rgba(16, 185, 129, 0.6);
        }
        .neon-shadow-red {
            box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4), 0 8px 10px -6px rgba(220, 38, 38, 0.4);
        }
        .neon-shadow-red:hover {
            box-shadow: 0 20px 35px -5px rgba(220, 38, 38, 0.6), 0 12px 15px -6px rgba(220, 38, 38, 0.6);
        }
        .neon-shadow-amber {
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4), 0 8px 10px -6px rgba(245, 158, 11, 0.4);
        }
        .neon-shadow-amber:hover {
            box-shadow: 0 20px 35px -5px rgba(245, 158, 11, 0.6), 0 12px 15px -6px rgba(245, 158, 11, 0.6);
        }
        /* Animation discrète pour le badge de rafraîchissement */
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse-element {
            animation: pulse-soft 2s infinite;
        }
    </style>

    <!-- Barre d'outils du haut : Filtres + Indicateur Live-Script -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100 dark:border-white/5 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Filtrer par :</span>

            <x-filament::button wire:click="setFilter('all')" color="{{ $statusFilter === 'all' ? 'primary' : 'gray' }}" size="sm" icon="heroicon-m-funnel">
                Toutes les chambres
            </x-filament::button>

            <x-filament::button wire:click="setFilter('disponible')" color="{{ $statusFilter === 'disponible' ? 'success' : 'gray' }}" size="sm" icon="heroicon-m-check-circle">
                Disponibles
            </x-filament::button>

            <x-filament::button wire:click="setFilter('occupee')" color="{{ $statusFilter === 'occupee' ? 'danger' : 'gray' }}" size="sm" icon="heroicon-m-user">
                Occupées (Calendrier)
            </x-filament::button>

            <x-filament::button wire:click="setFilter('menage')" color="{{ $statusFilter === 'menage' ? 'warning' : 'gray' }}" size="sm" icon="heroicon-m-sparkles">
                En ménage
            </x-filament::button>
        </div>

        <!-- Petit indicateur visuel géré en JS pour simuler la surveillance temps réel -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
            <span class="h-2 w-2 rounded-full bg-emerald-500 pulse-element"></span>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Surveillance Live Active</span>
        </div>
    </div>

    <!-- Conteneur Flexbox horizontal pleine largeur -->
    <div style="display: flex; flex-wrap: wrap; gap: 24px; width: 100%;">
        @forelse($chambres as $chambre)
            @php
                [$cardBg, $shadowClass, $textColor, $subTextColor, $badgeStyle, $icon, $statusLabel] = match($chambre->statut_calculer) {
                    'disponible' => [
                        'bg-gradient-to-br from-emerald-500 to-teal-600', 'neon-shadow-emerald',
                        'text-white', 'text-emerald-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-check-circle', 'Disponible'
                    ],
                    'occupee' => [
                        'bg-gradient-to-br from-red-500 to-rose-600', 'neon-shadow-red',
                        'text-white', 'text-rose-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-user', 'Occupée'
                    ],
                    'menage' => [
                        'bg-gradient-to-br from-amber-500 to-orange-500', 'neon-shadow-amber',
                        'text-white', 'text-amber-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-sparkles', 'En Ménage'
                    ],
                    default => [
                        'bg-gradient-to-br from-slate-500 to-slate-600', 'shadow-lg',
                        'text-white', 'text-slate-100',
                        'bg-black/20 text-white border-white/30', 'heroicon-o-no-symbol', 'Inconnu'
                    ]
                };
            @endphp

            <!-- Case de la chambre stylisée avec dégradé vif, ombres néon et animations -->
            <div class="hotel-card {{ $cardBg }} {{ $shadowClass }} p-6 rounded-2xl flex flex-col justify-between"
                 style="flex: 1 1 260px; max-w: 320px; min-height: 210px;">

                <div>
                    <!-- En-tête de case : Numéro augmenté et Badge -->
                    <div class="flex justify-between items-center">
                        <h3 class="text-3xl font-black {{ $textColor }} tracking-tight border-b-2 border-white/20 pb-0.5">
                            N° {{ $chambre->number }}
                        </h3>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-black uppercase rounded-lg border {{ $badgeStyle }}">
                            <x-filament::icon :icon="$icon" class="h-3.5 w-3.5" />
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <!-- Catégorie de chambre -->
                    <div class="mt-4 flex items-center gap-2 {{ $subTextColor }}">
                        <x-filament::icon icon="heroicon-o-home" class="h-4 w-4 opacity-90" />
                        <p class="text-xs font-black tracking-widest uppercase">
                            {{ $chambre->roomType->name }}
                        </p>
                    </div>

                    <!-- Bloc détails du planning -->
                    @if($chambre->statut_calculer === 'occupee')
                        <div class="mt-4 p-2.5 rounded-xl bg-black/30 text-white text-xs space-y-1 border border-white/10 shadow-inner backdrop-blur-sm">
                            <div class="truncate">👤 Client : <span class="font-bold text-white">{{ $chambre->client_actuel }}</span></div>
                            <div class="mt-1">📅 Libération : <span class="font-bold text-yellow-300">{{ $chambre->date_depart }}</span></div>
                        </div>
                    @endif
                </div>

                <!-- Pied de case : Bouton d'action épuré et arrondi -->
                <div class="mt-5 pt-3 border-t border-white/10 flex justify-end">
                    <x-filament::button
                        href="{{ url('/admin/bookings') }}"
                        tag="a"
                        color="white"
                        size="sm"
                        icon="heroicon-m-cog-6-tooth"
                        icon-position="before"
                        class="font-black text-gray-900 bg-white hover:bg-gray-100 shadow rounded-xl border-transparent"
                    >
                        Gérer
                    </x-filament::button>
                </div>

            </div>
        @empty
            <div class="w-full text-center py-12 text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-200 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-no-symbol" class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                <p class="text-sm font-medium">Aucune chambre ne correspond à ce critère actuellement.</p>
            </div>
        @endforelse
    </div>

    <!-- Script JavaScript pour le rafraîchissement intelligent sans clignotement -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Le script vérifie toutes les 10 secondes si des modifications ont eu lieu en BDD
            // En utilisant le moteur Livewire natif pour rafraîchir en douceur l'arrière-plan
            setInterval(() => {
                if (window.Livewire) {
                    window.Livewire.dispatch('refresh');
                }
            }, 10000);
        });
    </script>
</x-filament-panels::page>
