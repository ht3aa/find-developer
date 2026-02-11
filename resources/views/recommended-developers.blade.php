@extends('layouts.app')

@section('title', 'Recommended Developers')
@section('seo_title', 'FindDeveloper - Recommended Developers | Handpicked Top Developers')
@section('seo_description', 'Browse our handpicked selection of recommended developers. Top-tier talent ready to bring your projects to life.')
@section('seo_keywords', 'recommended developers, top developers, best developers, handpicked developers, premium developers, expert developers')

@section('content')
    <div id="recommended-app" data-developers="{{ json_encode($developersData) }}" data-is-admin="{{ $isAdmin ? '1' : '0' }}" data-is-logged-in="{{ $isLoggedIn ? '1' : '0' }}"></div>
    @vite('resources/js/recommended.js')
@endsection
