<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($this->chambres as $chambre)
            @php
                // Détermination de la couleur du bloc selon l'état du ménage
                $bgClass = match($chambre->housekeeping_status) {
                    'propre' => 'bg-emerald-50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800',
                    'sale' => 'bg-rose-50 border-rose-200 dark:bg-rose-950/20 dark:border-rose-800',
                    'en_cours' => 'bg-amber-50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-800',
                    'maintenance' => 'bg-gray-50 border-gray-200 dark:bg-gray-950/20 dark:border-gray-800',
                    default => 'bg-white border-gray-200'
                };

                $badgeColor = match($chambre->housekeeping_status) {
                    'propre' => 'text-emerald-700 bg-emerald-100',
                    'sale' => 'text-rose-700 bg-rose-100',
                    'en_cours' => 'text-amber-700 bg-amber-100',
                    'maintenance' => 'text-gray-700 bg-gray-100',
                    default => 'text-gray-700 bg-gray-100'
                };
            @endphp

            <div class="p-5 border rounded-xl shadow-sm flex flex-col justify-between {{ $bgClass }}">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Chambre N° {{ $chambre->number }}</h3>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                            {{ strtoupper($chambre->housekeeping_status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $chambre->roomType->name ?? 'Catégorie inconnue' }}</p>
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    @if($chambre->housekeeping_status === 'sale')
                        <button wire:click="démarrerMénage({{ $chambre->id }})"
                                class="w-full py-2 px-3 text-xs font-medium text-center text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                            🧹 Commencer le ménage
                        </button>
                    @endif

                    @if($chambre->housekeeping_status === 'en_cours' || $chambre->housekeeping_status === 'sale')
                        <button wire:click="marquerCommePropre({{ $chambre->id }})"
                                class="w-full py-2 px-3 text-xs font-medium text-center text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                            🧼 Marquer comme PROPRE
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
