<x-layouts.parkslot>

@if(Auth::check() && Auth::user()->isUser())
        <div class="w-full h-[420px] overflow-hidden">
            <img src="{{ asset('images/parking.jpg') }}" alt="Parking" class="w-full h-full object-cover">
        </div>

        <div class="text-white px-6 py-14 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-2">Réserver votre place de parking</h1>
            <div class="w-24 h-0.5 bg-white mx-auto my-5"></div>
            <p class="text-base md:text-lg text-white/90 mb-10 leading-relaxed">
                Réservez en un instant. Plus de places ? Pas d'inquiétude :<br>
                Vous êtes automatiquement positionné sur la prochaine place disponible.
            </p>
            <form action="{{ route('reservation.store') }}" method="POST" class="flex items-center max-w-3xl mx-auto bg-white rounded-full shadow-xl px-4 py-2">
                @csrf
                <input type="text" name="parking" placeholder="Chercher un parking" class="flex-1 text-gray-700 placeholder-gray-400 bg-transparent outline-none text-sm min-w-0 border-0 ring-0 focus:ring-0 focus:outline-none">
                <div class="w-px h-6 bg-gray-200 mx-3 flex-shrink-0"></div>
                <input type="text" name="immatriculation" placeholder="Immatriculation voiture" class="flex-1 text-gray-700 placeholder-gray-400 bg-transparent outline-none text-sm min-w-0 border-0 ring-0 focus:ring-0 focus:outline-none">
                <button type="submit" class="ml-3 flex-shrink-0 bg-black hover:bg-gray-900 text-white font-semibold text-sm px-6 py-2.5 rounded-full transition">Réserver</button>
            </form>@if (session('success'))
                <p class="text-green-400 text-sm mt-3 text-center">
                    {{ session('success') }}
                </p>
            @endif

            @if (session('info'))
                <p class="text-blue-400 text-sm mt-3 text-center">
                    {{ session('info') }}
                </p>
            @endif

        </div>

    @elseif(Auth::check() && Auth::user()->isAdmin())
    ici la page admin
@endif

</x-layouts.parkslot>
