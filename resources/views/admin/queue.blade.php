<x-layouts.parkslot>
    <main class="pt-28 px-6">

        <h1 class="text-4xl text-center text-white mb-8">File d'attente</h1>

        <div class="max-w-5xl mx-auto">


            <form method="GET" action="{{ route('fileattente') }}" class="mb-6 text-center">
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
                        <th class="pb-2">Immatriculation</th>
                        <th class="pb-2">Date</th>
                        <th class="pb-2">Utilisateur</th>
                        <th class="pb-2">Position</th>
                    </tr>
                    </thead>
                    <tbody id="queue-table">
                    @forelse($files as $file)
                        <tr class="border-t cursor-move" data-id="{{ $file->id }}">
                            <td class="py-2">{{ $file->parking->lib_parking }}</td>
                            <td class="py-2">{{ $file->voiture->immatriculation }}</td>
                            <td class="py-2">{{ $file->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2">{{ $file->voiture->user->prenom }} {{ $file->voiture->user->name }}</td>
                            <td class="py-2">{{ $file->position }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">Aucune donnée</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @section('scripts')
            <script>
                window.swapUrl = "{{ route('admin.queue.swap') }}";
            </script>
            <script src="{{ asset('js/DragSwap.js') }}"></script>
        @endsection
    </main>
</x-layouts.parkslot>
