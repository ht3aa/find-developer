@props(['developer', 'currentUserDeveloper' => null, 'recommendedDeveloperIds' => [], 'showRecommendedBadge' => false])

@php
    $isPremium = $developer->subscription_plan->value === 'premium';
    $isPro = $developer->subscription_plan->value === 'pro';
@endphp

@if($isPremium)
    <x-developer-card-premium :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" :showRecommendedBadge="$showRecommendedBadge" />
@elseif($isPro)
    <x-developer-card-pro :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" :showRecommendedBadge="$showRecommendedBadge" />
@else
    <x-developer-card-normal :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" :showRecommendedBadge="$showRecommendedBadge" />
@endif
