@extends('layouts.app')

@section('title', 'Companies')
@section('seo_title', 'Companies - FindDeveloper | Companies & Skills')
@section('seo_description', 'Browse companies and the skills they are currently working on. Discover tech stacks and hiring focus areas.')
@section('seo_keywords', 'companies, tech companies, developer skills, hiring, companies hiring developers')

@push('styles')
    @vite(['resources/css/companies.css', 'resources/css/developer-search.css'])
@endpush

@section('content')
    @livewire('companies')
@endsection
