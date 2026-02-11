@extends('layouts.app')

@section('title', 'Search Developers')
@section('seo_title', 'FindDeveloper - Find Your Perfect Developer | Developer Search Platform')
@section('seo_description', 'Search and discover talented developers in Iraq. Filter by skills, experience, location, and job title. Connect with developers for your projects.')
@section('seo_keywords', 'find developer, developer search, hire developer, developers in iraq, search developers, developer directory, web developer, mobile developer, software developer')

@section('content')
    <div id="search-hero-app"
         data-total-count="{{ \App\Models\Developer::count() }}"
         data-register-url="{{ route('register') }}">
    </div>
    @vite('resources/js/search-hero.js')
    @vite('resources/js/card-effects.js')

    @livewire('developer-search')
@endsection
