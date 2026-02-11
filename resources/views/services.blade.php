@extends('layouts.app')

@section('title', 'Services')
@section('seo_title', 'FindDeveloper - Services | Professional Development Services')
@section('seo_description', 'Browse professional development services offered by HR professionals and companies. Find the right service for your needs.')
@section('seo_keywords', 'services, development services, HR services, professional services, consulting')

@section('content')
    <div id="services-app" data-providers="{{ json_encode($providers) }}" data-badges-url="{{ route('badges') }}"></div>
    @vite('resources/js/services.js')
@endsection
