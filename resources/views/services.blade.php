@extends('layouts.app')

@section('title', 'Services')
@section('seo_title', 'FindDeveloper - Services | Professional Development Services')
@section('seo_description', 'Browse professional development services offered by HR professionals and companies. Find the right service for your needs.')
@section('seo_keywords', 'services, development services, HR services, professional services, consulting')

@push('styles')
@vite(['resources/css/services.css'])
@endpush

@section('content')
    @livewire('services')
@endsection
