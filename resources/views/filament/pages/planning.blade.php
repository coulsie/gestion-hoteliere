<x-filament-panels::page>
    <div class="p-6 bg-white rounded-2xl shadow-md border border-gray-100 dark:bg-gray-900 dark:border-gray-800 transition-all duration-300">

        <!-- En-tête du Planning Style Moderne -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <span>🗓️</span> Planning de l'Hôtel :
                    <span class="capitalize bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 px-3 py-1 rounded-lg text-lg font-bold border border-emerald-200/50 dark:border-emerald-800/30">
                        {{ $moisActuelTexte }}
                    </span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vue d'ensemble en temps réel des occupations et des chambres disponibles.</p>
            </div>

            <!-- Légende Éclatante -->
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-xl border border-gray-200/40 dark:border-gray-700/30">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-1">Légende :</span>
                <span class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[11px] font-bold rounded-lg shadow-sm">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> Séjour Classique
                </span>
                <span class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-[11px] font-bold rounded-lg shadow-sm">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> Passage Horaire
                </span>
            </div>
        </div>

        <!-- Grille d'occupation Haute Visibilité -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm" style="max-height: 650px;">
            <table class="w-full border-collapse text-left text-xs">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Colonne fixe Chambre Header -->
                        <th class="p-4 font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center border-r border-gray-200 dark:border-gray-700 sticky left-0 bg-gray-100 dark:bg-gray-800 z-30 min-w-[120px] shadow-[3px_0_5px_rgba(0,0,0,0.04)]">
                            Chambres
                        </th>
                        @foreach($joursDuMois as $jour)
                            @php
                                $isWeekend = $jour->isWeekend();
                                $isToday = $jour->isToday();
                            @endphp
                            <th class="p-2 border-r border-gray-200 dark:border-gray-700 text-center min-w-[50px] transition-colors
                                {{ $isToday ? 'bg-primary-50 dark:bg-primary-950/30' : ($isWeekend ? 'bg-gray-50 dark:bg-gray-800/40' : '') }}">
                                <div class="text-[10px] font-bold uppercase {{ $isWeekend ? 'text-rose-500 dark:text-rose-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $jour->isoFormat('ddd') }}
                                </div>
                                <div class="mt-1 flex items-center justify-center">
                                    @if($isToday)
                                        <span class="bg-gradient-to-r from-primary-500 to-primary-600 text-white font-black rounded-full w-7 h-7 flex items-center justify-center shadow-md animate-bounce ring-4 ring-primary-500/20">
                                            {{ $jour->day }}
                                        </span>
                                    @else
                                        <span class="text-sm font-bold {{ $isWeekend ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $jour->day }}
                                        </span>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($grilleOccupation as $item)
                        <tr class="transition-colors duration-150 hover:bg-gray-50/70 dark:hover:bg-gray-800/30">
                            <!-- Colonne fixe de la chambre stylisée -->
                            <td class="p-4 font-extrabold text-sm border-r border-gray-200 dark:border-gray-700 sticky left-0 bg-white dark:bg-gray-900 z-20 text-gray-900 dark:text-white shadow-[4px_0_6px_rgba(0,0,0,0.03)] flex items-center gap-2">
                                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                No. {{ $item['chambre'] }}
                            </td>

                            <!-- Cases des jours épurées -->
                            @foreach($joursDuMois as $jour)
                                @php
                                    $data = $item['planning'][$jour->format('Y-m-d')];
                                    $isWeekend = $jour->isWeekend();
                                    $isToday = $jour->isToday();
                                @endphp
                                <td class="p-1 border-r border-b border-gray-100 dark:border-gray-800 text-center relative h-14 transition-colors
                                    {{ $isToday ? 'bg-primary-50/20 dark:bg-primary-950/10' : ($isWeekend ? 'bg-gray-50/30 dark:bg-gray-800/10' : '') }}">

                                    @if($data)
                                        <!-- Blocs de Réservations Éclatants avec dégradés de couleurs -->
                                        <a href="{{ url('/admin/bookings/' . $data['id'] . '/edit') }}"
                                           class="absolute inset-1 p-1.5 rounded-xl text-[10px] font-black text-white flex flex-col items-center justify-center overflow-hidden transition-all duration-200 hover:scale-[1.04] hover:shadow-md shadow-sm border
                                           {{ $data['type'] === 'passage'
                                                ? 'bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 border-orange-400 dark:border-orange-600 shadow-orange-500/20'
                                                : 'bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 border-blue-400 dark:border-blue-600 shadow-blue-500/20' }}"
                                           title="Client: {{ $data['client'] }} (Cliquez pour modifier ou encaisser)">

                                            <!-- Icône et nom épurés -->
                                            <span class="block truncate max-w-full text-center tracking-wide uppercase">
                                                {{ $data['type'] === 'passage' ? '🕒' : '👤' }} {{ Str::limit($data['client'], 8, '..') }}
                                            </span>
                                        </a>
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
