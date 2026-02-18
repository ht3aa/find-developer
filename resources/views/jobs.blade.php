@extends('layouts.app')

@section('title', 'Job Opportunities')
@section('seo_title', 'FindDeveloper - Job Opportunities | Find Your Next Career Move')
@section('seo_description', 'Browse available job opportunities for developers. Find your next career move with our curated job listings.')
@section('seo_keywords', 'jobs, developer jobs, programming jobs, software jobs, career opportunities, job listings')

@push('styles')
@vite(['resources/css/company-job-search.css'])
@endpush

@section('content')
    @livewire('company-job-search')
@endsection
