<x-layouts.parkslot>
    <main class="pt-28 px-6">

        <h1 class="text-4xl text-center text-white mb-8">Places occupées</h1>

        <div class="max-w-5xl mx-auto">

            <form method="GET" action="{{ route('places') }}" class="mb-6 text-center">
                <select name="parking_id" onchange="this.form.submit()"
                        class="px-6 py-2 rounded-full bg-gray-200 text-gray-700">
                    <option value="" disabled selected>Choisir un parking</option>
                    @foreach($parkings as $parking)
                        <option value="{{ $parking->id }}"
                            {{ $parkingId == $parking->id ? 'selected' : '' }}>
                            {{ $parking->lib_parking }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="bg-gray-200 rounded-3xl p-6">
                <table class="w-full text-left">
                    <thead>
                    <tr class="text-sm text-gray-700 border-b">
                        <th class="pb-2">Parking</th>
                        <th class="pb-2">Place</th>
                        <th class="pb-2">Immatriculation</th>
                        <th class="pb-2">Utilisateur</th>
                        <th class="pb-2">Fin réservation</th>
                        <th class="pb-2 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->place->parking->lib_parking }}</td>
                            <td>P{{ $reservation->place->num_place }}</td>
                            <td>{{ $reservation->voiture->immatriculation }}</td>
                            <td>{{ $reservation->voiture->user->prenom }} {{ $reservation->voiture->user->name }}</td>
                            <td>{{ $reservation->fin_reservation->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('places.destroy', $reservation->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-black text-white px-4 py-1 rounded hover:bg-gray-800">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Aucune réservation</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</x-layouts.parkslot>
