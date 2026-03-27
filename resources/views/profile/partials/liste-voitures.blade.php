<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Mes voitures') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Liste des véhicules enregistrés sur votre compte.') }}
        </p>
    </header>

    <div class="mt-6 space-y-4">
        @forelse ($voituresUser as $voiture)
            <div class="flex items-center justify-between p-4 border rounded-lg bg-white shadow-sm hover:shadow transition">
                <div>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $voiture->modele_voiture }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $voiture->immatriculation }}
                    </p>
                </div>
            </div>
        @empty
            <div class="p-4 border rounded-lg bg-gray-50 text-sm text-gray-600">
                {{ __('Aucune voiture enregistrée.') }}
            </div>
        @endforelse
    </div>
</section>
