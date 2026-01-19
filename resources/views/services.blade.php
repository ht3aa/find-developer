@extends('layouts.app')

@section('title', 'Services')
@section('seo_title', 'FindDeveloper - Services | Professional Development Services')
@section('seo_description', 'Browse professional development services offered by HR professionals and companies. Find the right service for your needs.')
@section('seo_keywords', 'services, development services, HR services, professional services, consulting')

@push('styles')
<link href="{{ asset('css/services.css') }}" rel="stylesheet">
@endpush

@section('content')
    @livewire('services')
@endsection
