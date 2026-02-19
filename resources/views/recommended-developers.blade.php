@extends('layouts.app')

@push('styles')
    @vite(['resources/css/developer-search.css'])
@endpush

@section('title', 'Recommended Developers')
@section('seo_title', 'FindDeveloper - Recommended Developers | Handpicked Top Developers')
@section('seo_description', 'Browse our handpicked selection of recommended developers. Top-tier talent ready to bring your projects to life.')
@section('seo_keywords', 'recommended developers, top developers, best developers, handpicked developers, premium developers, expert developers')

@section('content')
    @livewire('recommended-developers')
@endsection
