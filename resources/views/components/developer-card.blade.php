@props(['developer'])

@php
    $isPremium = $developer->subscription_plan->value === 'premium';
    $isPro = $developer->subscription_plan->value === 'pro';
@endphp

@if($isPremium)
    <x-developer-card-premium :developer="$developer" />
@elseif($isPro)
    <x-developer-card-pro :developer="$developer" />
@else
    <x-developer-card-normal :developer="$developer" />
@endif
