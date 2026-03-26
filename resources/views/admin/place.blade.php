<x-layouts.parkslot>
    <main class="pt-28 px-4 pb-12">

        <h1 class="text-4xl text-center text-white font-bold mb-2">Places occupées</h1>
        <p class="text-center text-white/60 text-sm mb-8">Réservations en cours — libérez une place pour la remettre à disposition.</p>

        <div class="max-w-6xl mx-auto">

            {{-- ── Alertes ──────────────────────────────────── --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 text-sm rounded-xl px-5 py-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 text-sm rounded-xl px-5 py-4">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- ── Filtre parking ───────────────────────────── --}}
            <form method="GET" action="{{ route('places') }}" class="mb-6 flex gap-3 items-center">
                <select name="parking_id" onchange="this.form.submit()"
                        class="rounded-full bg-white text-gray-700 text-sm px-5 py-2.5 focus:outline-none shadow-sm">
                    <option value="">Tous les parkings</option>
                    @foreach($parkings as $parking)
                        <option value="{{ $parking->id }}"
                            {{ $parkingId == $parking->id ? 'selected' : '' }}>
                            {{ $parking->lib_parking }}
                        </option>
                    @endforeach
                </select>

                @if($parkingId)
                    <a href="{{ route('places') }}"
                       class="text-white/70 hover:text-white text-sm underline">
                        Réinitialiser
                    </a>
                @endif

                <span class="ml-auto text-white/70 text-sm">
                    {{ $reservations->count() }} place(s) occupée(s)
                </span>
            </form>

            {{-- ── Tableau réservations actives ─────────────── --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg mb-8">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-5 py-4">Parking</th>
                        <th class="px-5 py-4">Place</th>
                        <th class="px-5 py-4">Utilisateur</th>
                        <th class="px-5 py-4">Immatriculation</th>
                        <th class="px-5 py-4">Début</th>
                        <th class="px-5 py-4">Expire le</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reservations as $reservation)
                        @php
                            $expire  = $reservation->fin_reservation;
                            $expired = $expire && $expire->isPast();
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
                            </td>
                            <td class="px-5 py-3 font-mono text-xs tracking-wider">
                                {{ $reservation->voiture?->immatriculation ?? '–' }}
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                {{ $reservation->debut_reservation?->format('d/m H\hi') ?? '–' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($expire)
                                    <span class="{{ $expired ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $expire->format('d/m H\hi') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">–</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST"
                                      action="{{ route('places.destroy', $reservation->id) }}"
                                      onsubmit="return confirm('Libérer la place P{{ $reservation->place?->num_place }} et clôturer la réservation ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                        Libérer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                                Aucune place occupée{{ $parkingId ? ' pour ce parking' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Lien vers la gestion des places ─────────── --}}
            <div class="text-center">
                <a href="{{ route('places.gestion') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-800 font-semibold text-sm px-6 py-3 rounded-full shadow hover:bg-gray-100 transition">
                    ⚙️ Gérer les places (ajouter / modifier / supprimer)
                </a>
            </div>

        </div>
    </main>
</x-layouts.parkslot>
