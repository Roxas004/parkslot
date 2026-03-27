<x-layouts.parkslot>

    <div class="max-w-6xl mx-auto px-6 py-10 pt-28">

        <h1 class="text-4xl font-bold text-white text-center mb-8">Gestion des utilisateurs</h1>

        {{-- Alertes --}}
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-300 text-green-800 text-sm rounded-xl px-5 py-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Recherche et filtre en direct --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <input type="text"
                   id="search"
                   placeholder="Rechercher par nom, prénom, email…"
                   class="flex-1 min-w-[220px] rounded-full border-0 bg-white text-gray-800
                          placeholder-gray-400 text-sm px-5 py-2.5 focus:outline-none shadow-sm">

            <select id="filter"
                    class="rounded-full border-0 bg-white text-gray-700 text-sm px-5 py-2.5
                           focus:outline-none shadow-sm">
                <option value="">Tous</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvés</option>
            </select>
        </div>

        {{-- Tableau --}}
        <div id="results" class="bg-white rounded-2xl overflow-hidden shadow-lg">
            <table class="w-full text-sm text-left text-gray-800">
                <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-5 py-4">Nom</th>
                    <th class="px-5 py-4">Email</th>
                    <th class="px-5 py-4 text-center">Statut</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
                </thead>
                <tbody id="table-body">
                @foreach ($users as $user)
                    <tr data-status="{{ $user->approved ? 'approved' : 'pending' }}">
                        <td class="px-5 py-4 font-medium">{{ $user->prenom }} {{ $user->name }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ $user->email }}</td>
                        <td class="px-5 py-4 text-center">
                            @if ($user->approved)
                                <span class=" text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Approuvé
                                </span>
                            @else
                                <span class=" text-orange-600 text-xs font-semibold px-3 py-1 rounded-full">
                                    En attente
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">

                                @if (! $user->approved)
                                    <form method="POST" action="{{ route('utilisateurs.accepter', $user) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs font-medium bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                                            Accepter
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('utilisateurs.refuser', $user) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs px-4 font-medium bg-blue-800 hover:bg-blue-700 text-white  py-1.5 rounded-lg transition">
                                            Refuser
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('utilisateurs.reset-mdp', $user) }}"
                                      onsubmit="return confirm('Envoyer un email de réinitialisation à {{ $user->email }} ?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs font-medium bg-gray-900 hover:bg-gray-700 text-white px-3 py-1.5 rounded-lg transition">
                                        Reset MDP
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('utilisateurs.supprimer', $user) }}"
                                      onsubmit="return confirm('Supprimer définitivement {{ $user->prenom }} {{ $user->name }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                        Supprimer
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-sm text-white/60 text-right">
            {{ $users->count() }} utilisateur(s) affiché(s)
        </p>

    </div>

    @section('scripts')
        <script src="{{ asset('js/GererUser.js') }}"></script>
    @endsection

</x-layouts.parkslot>
