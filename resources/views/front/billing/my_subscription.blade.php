<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi suscripción
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Session::has('notification'))
                <p class="p-2 my-2 border border-gree-600 bg-green-100 text-green-600 font-bold rounded-lg ">{{ Session::get('notification') }}</p>
             @endif

            <div class="my-8 divide-y-2 divide-gray-100 bg-gray-400 p-4">
                <div class="py-8 flex flex-wrap md:flex-nowrap">
                    <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                        <span class="font-semibold title-font text-white">Plan contratado: {{ $subscription }}</span>
                    </div>
                    <div class="md:flex-grow">
                        <a target="_blank" href="{{route('billing.portal')}}" class="text-white bg-red-500 border-0 py-2 px-4 focus:outline-none hover:bg-red-600 rounded">Ver mi facturación</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
