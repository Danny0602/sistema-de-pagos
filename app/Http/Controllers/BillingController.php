<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Country;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Cashier\Exceptions\IncompletePayment;

class BillingController extends Controller
{
    public function paymentMethodForm(): Renderable
    {
        $countries = Country::all();
        return view('front.billing.payment_method_form', [
            'intent' => auth()->user()->createSetupIntent(),
            'countries' => $countries,
        ]);
    }

    public function processPaymentMethod(): RedirectResponse
    {
        $this->validate(request(), [
            "pm" => "required|string|max:50|starts_with:pm_",
            "card_holder_name" => "required|max:150|string",
            "country_id" => "required|exists:countries,id",
        ]);

        // dd(request()->input());

        if (!auth()->user()->hasStripeId()) {
            auth()->user()->createAsStripeCustomer([
                "address" => [
                    "country" => Country::find(request("country_id"))->code,
                ]
            ]);
        }
        auth()->user()->updateDefaultPaymentMethod(request("pm"));
        session()->flash('notification', 'Metodo de pago actualizado correctamente');
        return redirect()->back();
    }


    public function plans()
    {
      

        $key = config('cashier.secret');
        $stripe = new StripeClient($key);

        $plans = $stripe->plans->all();
        $plans  = $plans->data;
        $planes = array_reverse($plans);
        // dd($planes);



        return view('front.billing.plans', [
            'planes' => $planes,

        ]);
    }


    public function processSubscription(Request $request)
    {
        $validated = $request->validate([
            'price_id' => 'required',

        ]);


        $plan_id = $validated['price_id'];
        $key = config('cashier.secret');
        $stripe = new StripeClient($key);
        $plan = $stripe->plans->retrieve($plan_id);


        //procesar pago
        try {

            auth()->user()->newSubscription('default', $plan_id)->create();

            session()->flash('notification', 'Te has inscrito correctamente a la membresia ' . getPlanNameByStripePlan($plan));

            return redirect()->route('billing.my_subscription');
        } catch (IncompletePayment $exception) {
            //Error al completar pago - 3D Secure
            session()->flash('notification', 'Te has inscrito correctamente a la membresia ' . getPlanNameByStripePlan($plan));

            return redirect()->route('cashier.payment', [$exception->payment->id, 'redirect' => route('billing.my_subscription')]);
        } catch (Exception $exception) {
            //Error

            session()->flash('notification', $exception->getMessage());
            return back();
        };
    }


    public function mySubscription(): Renderable
    {
        $subscription = getSubscriptionNameForUser();
        return view("front.billing.my_subscription", compact("subscription"));
    }
}
