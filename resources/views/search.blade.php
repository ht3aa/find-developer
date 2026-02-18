@extends('layouts.app')

@push('styles')
    @vite(['resources/css/developer-search.css'])
@endpush

@section('title', 'Search Developers')
@section('seo_title', 'FindDeveloper - Find Your Perfect Developer | Developer Search Platform')
@section('seo_description', 'Search and discover talented developers in Iraq. Filter by skills, experience, location, and job title. Connect with developers for your projects.')
@section('seo_keywords', 'find developer, developer search, hire developer, developers in iraq, search developers, developer directory, web developer, mobile developer, software developer')

@section('content')
    @livewire('developer-search')
@endsection
