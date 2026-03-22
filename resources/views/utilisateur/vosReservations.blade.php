<x-layouts.parkslot>

    <div class="max-w-6xl mx-auto px-6 py-10 pt-28 text-white">

        <h1 class="text-6xl font-bold text-center mb-10">Vos réservations</h1>

        <h2 class="text-2xl font-semibold mb-4">En cours :</h2>

        <div class="bg-white rounded-2xl overflow-hidden mb-10">
            @if ($reservationsActives->isEmpty() && $fileAttente->isEmpty())
                <p class="px-6 py-6 text-gray-500 text-sm text-center">Aucune réservation active.</p>
            @else
                <table class="w-full text-sm text-left text-gray-800">
                    <thead>
                    <tr class="text-gray-800 font-semibold border-b border-gray-200">
                        <th class="px-4 py-3">Parking</th>
                        <th class="px-4 py-3">N° Place</th>
                        <th class="px-4 py-3">Immatriculation</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Heure début</th>
                        <th class="px-4 py-3">Heure fin</th>
                        <th class="px-4 py-3 text-center">File d'attente</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($reservationsActives as $reservation)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $reservation->place?->parking?->lib_parking ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->place?->num_place ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->voiture?->immatriculation ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->debut_reservation->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $reservation->debut_reservation->format('H\hi') }}</td>
                            <td class="px-4 py-3">{{ $reservation->fin_reservation?->format('H\hi') ?? '–' }}</td>
                            <td class="px-4 py-3 text-center">/</td>
                        </tr>
                    @endforeach

                    @foreach ($fileAttente as $entree)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $entree->parking?->lib_parking ?? 'N/A' }}</td>
                            <td class="px-4 py-3">–</td>
                            <td class="px-4 py-3">{{ $entree->voiture?->immatriculation ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $entree->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">–</td>
                            <td class="px-4 py-3">–</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $entree->position }}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>

        <h2 class="text-2xl font-semibold mb-4">Historique des réservations :</h2>

        <div class="bg-white rounded-2xl overflow-hidden mb-10">
            @if ($historique->isEmpty())
                <p class="px-6 py-6 text-gray-500 text-sm text-center">Aucune réservation passée.</p>
            @else
                <table class="w-full text-sm text-left text-gray-800">
                    <thead>
                    <tr class="text-gray-800 font-semibold border-b border-gray-200">
                        <th class="px-4 py-3">Parking</th>
                        <th class="px-4 py-3">N° Place</th>
                        <th class="px-4 py-3">Immatriculation</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Heure début</th>
                        <th class="px-4 py-3">Heure fin</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($historique as $reservation)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $reservation->place?->parking?->lib_parking ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->place?->num_place ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->voiture?->immatriculation ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $reservation->debut_reservation->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $reservation->debut_reservation->format('H\hi') }}</td>
                            <td class="px-4 py-3">{{ $reservation->fin_reservation->format('H\hi') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

</x-layouts.parkslot>
