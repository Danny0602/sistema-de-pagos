<x-app-layout>
    <x-slot name="header">
        <h2 class="gont-semibold text-xl text-gray-800 leading-tight">Actualiza tu metodo de Pago</h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

             @if (Session::has('notification')) 
                <p class="p-2 border border-gree-600 bg-green-100 text-green-600 font-bold rounded-lg ">{{ Session::get('notification') }}</p>
             @endif 
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-10 border border-gray-200 ">

                    <div class="mx-auto w-2/4">
                        <label for="card-name" class="w-full text-gray-500 font-bold px-1">
                            Nombre del Titular de la tarjeta
                        </label>

                        <input placeholder="Titular" id="card-name" name="card-name"
                            class="w-full p-2 bg-white rounded border border-gray-300 focus:border-indigo-100">
                    </div>
                    <div class="mx-auto w-2/4 my-5">
                        <label for="card-country" class="w-full text-gray-500 font-bold px-1">
                            Seleccione su Pais o Ciudad </label>

                        <select placeholder="Titular" id="country" name="country"
                            class="w-full p-2 bg-white rounded border border-gray-300 focus:border-indigo-100">
                            <option value="">-- Seleccione --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stripe Elements Placeholder -->
                    <div id="card-element" class="mx-auto w-2/4 mt-5 border py-3 px-2 border-gray-300 rounded">

                    </div>

                    <div class="mx-auto w-2/4 mt-16">


                        <button id="card-button" data-secret="{{ $intent->client_secret }}"
                            class="w-full py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-lg  rounded-lg ">
                            Agregar

                        </button>
                    </div>

                    <form id="payment_method_form" method="post" action="{{ route('billing.payment_method') }}">
                        @csrf

                        <input type="hidden" id="card_holder_name" name="card_holder_name">
                        <input type="hidden" id="pm" name="pm">
                        <input type="hidden" id="country_id" name="country_id">

                    </form>


                </div>


            </div>

        </div>
    </div>


    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            const stripe = Stripe('{{ config('cashier.key') }}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');



            const country = document.getElementById('country');
            const cardName = document.getElementById('card-name');
            const Button = document.getElementById('card-button');
            const clientSecret = Button.dataset.secret;

            Button.addEventListener('click', async (e) => {
                const {
                    setupIntent,
                    error
                } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: cardName.value
                            }
                        }
                    }
                );
                if (error) {
                    alert(error.message)
                } else {
                    // procesar con el controlador
                    document.getElementById("pm").value = setupIntent.payment_method;
                    document.getElementById("card_holder_name").value = cardName.value;
                    document.getElementById("country_id").value = country.value;
                    document.getElementById("payment_method_form").submit();


                }
            });
        </script>
    @endpush
</x-app-layout>
