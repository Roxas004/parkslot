<x-layouts.parkslot>
    <main class="pt-28 px-4 pb-12">

        <div class="max-w-6xl mx-auto">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl text-white font-bold">Gestion des places</h1>
                    <p class="text-white/60 text-sm mt-1">Ajoutez, modifiez ou supprimez des places de parking.</p>
                </div>
                <a href="{{ route('places') }}"
                   class="text-white/70 hover:text-white text-sm underline">
                    ← Places occupées
                </a>
            </div>

            @if ($errors->has('error'))
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 text-sm rounded-xl px-5 py-4">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ajouter une ou plusieurs places</h2>

                <form method="POST" action="{{ route('places.store') }}"
                      class="flex flex-wrap gap-4 items-end">
                    @csrf

                    <div class="flex flex-col gap-1 flex-1 min-w-[160px]">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            Parking <span class="text-red-500">*</span>
                        </label>
                        <select name="parking_id" required
                                class="rounded-lg border border-gray-300 text-gray-700 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Choisir un parking</option>
                            @foreach($parkings as $parking)
                                <option value="{{ $parking->id }}"
                                    {{ old('parking_id', $parkingId) == $parking->id ? 'selected' : '' }}>
                                    {{ $parking->lib_parking }}
                                </option>
                            @endforeach
                        </select>
                        @error('parking_id')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="bg-gray-900 hover:bg-gray-800 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition">
                        + Ajouter
                    </button>
                </form>
            </div>

            <form method="GET" action="{{ route('places.gestion') }}" class="mb-4 flex gap-3 items-center">
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
                    <a href="{{ route('places.gestion') }}"
                       class="text-white/70 hover:text-white text-sm underline">
                        Tous
                    </a>
                @endif

                <span class="ml-auto text-white/70 text-sm">
                    {{ $places->count() }} place(s)
                </span>
            </form>

            <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-5 py-4">Parking</th>
                        <th class="px-5 py-4">N° Place</th>
                        <th class="px-5 py-4 text-center">Statut</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($places as $place)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition" id="row-{{ $place->id }}">
                            <td class="px-5 py-3 font-medium">
                                {{ $place->parking?->lib_parking ?? '–' }}
                            </td>

                            <td class="px-5 py-3" id="num-display-{{ $place->id }}">
                                <span class="font-mono font-semibold">P{{ $place->num_place }}</span>
                            </td>

                            <td class="px-5 py-3 text-center">
                                @if($place->disponible)
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                        Disponible
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                                        Occupée
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">

                                    @if($place->disponible)
                                        <button type="button"
                                                onclick="toggleEdit({{ $place->id }})"
                                                class="text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                                            Modifier
                                        </button>
                                    @endif

                                    @if($place->disponible)
                                        <form method="POST"
                                              action="{{ route('places.destroyPlace', $place->id) }}"
                                              onsubmit="return confirm('Supprimer définitivement la place P{{ $place->num_place }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs font-medium bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                                Supprimer
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Occupée</span>
                                    @endif

                                </div>
                            </td>
                        </tr>

                        @if($place->disponible)
                            <tr id="edit-row-{{ $place->id }}" class="hidden bg-blue-50 border-b border-blue-100">
                                <td class="px-5 py-3 text-gray-500 italic text-xs">Modification</td>
                                <td class="px-5 py-3" colspan="3">
                                    <form method="POST"
                                          action="{{ route('places.update', $place->id) }}"
                                          class="flex items-center gap-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text"
                                               name="num_place"
                                               value="{{ $place->num_place }}"
                                               required
                                               maxlength="20"
                                               class="rounded-lg border border-blue-300 text-gray-700 text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-32">
                                        <button type="submit"
                                                class="text-xs font-medium bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                                            Enregistrer
                                        </button>
                                        <button type="button"
                                                onclick="toggleEdit({{ $place->id }})"
                                                class="text-xs font-medium bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                                            Annuler
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                                Aucune place trouvée{{ $parkingId ? ' pour ce parking' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    @section('scripts')
        <script>
            function toggleEdit(id) {
                const editRow = document.getElementById('edit-row-' + id);
                editRow.classList.toggle('hidden');
            }
        </script>
    @endsection

</x-layouts.parkslot>
