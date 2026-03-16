<nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

        <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">
        <img src="images/parkslot.png" class="h-8 w-auto"/>
    </span>
        </a>


        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse items-center">

            {{-- Si utilisateur connecté --}}
            @auth
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                                           text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                                                 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0
                                                 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <!-- Déconnexion -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Se déconnecter') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

            @else
                <div class="flex space-x-2">
                    <a href="{{ route('login') }}"
                       class="text-white bg-blue-950 hover:bg-blue-800 focus:ring-4 focus:outline-none
                              focus:ring-blue-700 font-medium rounded-lg text-sm px-4 py-2 text-center
                              dark:bg-blue-900 dark:hover:bg-blue-700 dark:focus:ring-blue-700">
                        {{ __('Se connecter') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="text-blue-950 border border-blue-950 hover:bg-blue-950 hover:text-white
                                  focus:ring-4 focus:outline-none focus:ring-blue-700 font-medium rounded-lg
                                  text-sm px-4 py-2 text-center dark:border-blue-500 dark:text-blue-800
                                  dark:hover:bg-blue-600 dark:hover:text-white dark:focus:ring-blue-700">
                            {{ __('S\'inscrire') }}
                        </a>
                    @endif
                </div>
            @endauth

        </div>
        <div class=" hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">

                <li>
                    <a href="{{ url('/vosreservations') }}"
                       class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100
                              md:hover:bg-transparent md:hover:text-blue-700 md:p-0
                              dark:text-white dark:hover:bg-gray-700 dark:hover:text-white
                              md:dark:hover:bg-transparent">Vos reservations</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
