@extends('layouts.app')

@section('title', 'Get Experience')
@section('seo_title', 'Get Experience - FindDeveloper | Build Experience with Small Tasks')
@section('seo_description', 'Browse small tasks to build your experience, earn XP, and grow as a developer. Find tasks that match your skills and availability.')
@section('seo_keywords', 'get experience, developer tasks, build experience, earn XP, practice projects, developer experience')

@section('content')
    <div id="experience-tasks-app" data-tasks="{{ json_encode($tasks) }}"></div>
    @vite('resources/js/experience-tasks.js')
@endsection
