@extends('layouts.app')

@section('title', 'Pricing Plans')
@section('seo_title', 'Pricing Plans - FindDeveloper | Developer & Student Plans')
@section('seo_description', 'Choose the perfect plan for developers or students. Free developer profiles, Pro plans, Premium plans, and student portfolio packages. Affordable pricing in IQD.')
@section('seo_keywords', 'developer plans, pricing plans, student portfolio, developer subscription, pro plan, premium plan, portfolio packages, developer pricing')

@section('content')
    <div id="plans-app"></div>
    @vite('resources/js/plans.js')
@endsection
