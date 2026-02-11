@extends('layouts.app')

@section('title', 'Special Needs Developers')
@section('seo_title', 'FindDeveloper - Special Needs Developers | Developer Search Platform')
@section('seo_description', 'Search and discover talented special needs developers. Filter by skills, experience, location, and job title.')
@section('seo_keywords', 'special needs developers, developer search, hire developer, developer directory')

@section('content')
    @livewire('special-needs-developer-search')
@endsection
