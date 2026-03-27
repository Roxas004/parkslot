<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ajouter une voiture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Renseignez l’immatriculation du véhicule à ajouter à votre compte.') }}
        </p>
    </header>

    <form method="post" action="{{ route('voitures.store') }}" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="immatriculation" :value="__('Immatriculation')" />
            <x-text-input id="immatriculation" name="immatriculation" type="text" class="mt-1 block w-full" required autocomplete="off" />
            <x-input-error :messages="$errors->get('immatriculation')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="modelvoiture" :value="__('Model Voiture')" />
            <x-text-input id="modelvoiture" name="modelvoiture" type="text" class="mt-1 block w-full" required autocomplete="off" />
            <x-input-error :messages="$errors->get('modelvoiture')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Ajouter') }}
            </x-primary-button>

        </div>
    </form>
</section>
