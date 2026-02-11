@extends('layouts.app')

@section('title', 'Badges')
@section('seo_title', 'Badges - FindDeveloper | Developer Achievement Badges')
@section('seo_description', 'Discover the badges you can earn as a developer. Learn about the benefits of each badge and how to achieve them.')
@section('seo_keywords', 'badges, developer badges, achievements, developer recognition, skill badges')

@section('content')
    <div id="badges-app" data-badges="{{ json_encode($badges) }}"></div>
    @vite('resources/js/badges.js')
@endsection
