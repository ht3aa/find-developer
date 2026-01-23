@extends('layouts.app')

@section('seo_title', 'Get Experience - FindDeveloper | Build Experience with Small Tasks')
@section('seo_description', 'Browse small tasks to build your experience, earn XP, and grow as a developer. Find tasks that match your skills and availability.')
@section('seo_keywords', 'get experience, developer tasks, build experience, earn XP, practice projects, developer experience')

@push('styles')
<link href="{{ asset('css/experience-tasks.css') }}" rel="stylesheet">
@endpush

@section('content')
    @livewire('experience-tasks-list')
@endsection
