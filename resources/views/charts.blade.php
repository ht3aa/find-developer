@extends('layouts.app')

@section('title', 'Charts & Analytics')
@section('seo_title', 'FindDeveloper - Analytics & Statistics | Developer Insights')
@section('seo_description', 'View analytics and statistics about developers, jobs, and platform data. Visual insights into developer status, subscription plans, locations, and experience levels.')
@section('seo_keywords', 'developer analytics, developer statistics, developer insights, job statistics, developer charts')

@push('styles')
@vite(['resources/css/charts.css'])
@endpush

@section('content')
    @livewire('charts')
@endsection
