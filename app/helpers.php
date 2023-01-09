<?php

use Stripe\StripeClient;


function getPlanNameByStripePlan(\Stripe\Plan $plan): string
{
    if ($plan->interval_count === 3) {
        return "Trimestral";
    } else {
        if ($plan->interval === "year") {
            return "Anual";
        } else {
            return "Mensual";
        }
    }
}



function getSubscriptionNameForUser(): string
{
    if (isSubscribed()) {
        $subscription = auth()->user()->subscription();
        $key = config('cashier.secret');
        $stripe = new StripeClient($key);
        $plan = $stripe->plans->retrieve($subscription->stripe_price);
        return getPlanNameByStripePlan($plan);
    }
    return "N/D";
}


function isSubscribed()
{
    return auth()->check() && auth()->user()->subscribed();
}
