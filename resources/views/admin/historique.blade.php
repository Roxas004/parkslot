<x-layouts.parkslot>
    <main class="pt-28 px-4 pb-12">

        <h1 class="text-4xl text-center text-white font-bold mb-8">Historique des attributions</h1>

        <div class="max-w-7xl mx-auto">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 text-sm rounded-xl px-5 py-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('historique') }}"
                  class="mb-6 flex flex-wrap gap-3 items-end">

                <div class="flex flex-col gap-1">
                    <label class="text-white text-xs font-semibold uppercase tracking-wide">Parking</label>
                    <select name="parking_id" onchange="this.form.submit()"
                            class="rounded-full bg-white text-gray-700 text-sm px-5 py-2.5 focus:outline-none shadow-sm min-w-[180px]">
                        <option value="">Tous les parkings</option>
                        @foreach($parkings as $parking)
                            <option value="{{ $parking->id }}"
                                {{ $parkingId == $parking->id ? 'selected' : '' }}>
                                {{ $parking->lib_parking }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($parkingId && $places->isNotEmpty())
                    <div class="flex flex-col gap-1">
                        <label class="text-white text-xs font-semibold uppercase tracking-wide">Place</label>
                        <select name="num_place"
                                class="rounded-full bg-white text-gray-700 text-sm px-5 py-2.5 focus:outline-none shadow-sm">
                            <option value="">Toutes les places</option>
                            @foreach($places as $place)
                                <option value="{{ $place->num_place }}"
                                    {{ $numPlace == $place->num_place ? 'selected' : '' }}>
                                    Place {{ $place->num_place }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="num_place" value="">
                @endif

                <div class="flex flex-col gap-1 flex-1 min-w-[200px]">
                    <label class="text-white text-xs font-semibold uppercase tracking-wide">Utilisateur</label>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Nom, prénom, email…"
                           class="rounded-full bg-white text-gray-700 text-sm px-5 py-2.5 focus:outline-none shadow-sm w-full">
                </div>

                <button type="submit"
                        class="bg-white text-gray-800 font-semibold text-sm px-6 py-2.5 rounded-full shadow-sm hover:bg-gray-100 transition">
                    Filtrer
                </button>

                @if($parkingId || $numPlace || $search)
                    <a href="{{ route('historique') }}"
                       class="text-white/70 hover:text-white text-sm underline self-center">
                        Réinitialiser
                    </a>
                @endif
            </form>

            <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-5 py-4">Parking</th>
                        <th class="px-5 py-4">Place</th>
                        <th class="px-5 py-4">Utilisateur</th>
                        <th class="px-5 py-4">Immatriculation</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Début</th>
                        <th class="px-5 py-4">Fin</th>
                        <th class="px-5 py-4 text-center">Durée</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reservations as $reservation)
                        @php
                            $debut   = $reservation->debut_reservation;
                            $fin     = $reservation->fin_reservation;
                            $duree   = $debut && $fin
                                ? $debut->diff($fin)->format('%Hh%I')
                                : '–';
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="px-5 py-3 font-medium">
                                {{ $reservation->place?->parking?->lib_parking ?? '–' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                                    P{{ $reservation->place?->num_place ?? '–' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                {{ $reservation->voiture?->user?->prenom ?? '–' }}
                                {{ $reservation->voiture?->user?->name ?? '' }}
                                <div class="text-xs text-gray-400">{{ $reservation->voiture?->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs tracking-wider">
                                {{ $reservation->voiture?->immatriculation ?? '–' }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $debut?->format('d/m/Y') ?? '–' }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $debut?->format('H\hi') ?? '–' }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $fin?->format('H\hi') ?? '–' }}
                            </td>
                            <td class="px-5 py-3 text-center text-gray-500 tabular-nums">
                                {{ $duree }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                                Aucune réservation trouvée.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $reservations->links() }}
                </div>
            @endif

            <p class="mt-4 text-sm text-white/60 text-right">
                {{ $reservations->total() }} réservation(s) au total
            </p>

        </div>
    </main>
</x-layouts.parkslot>
