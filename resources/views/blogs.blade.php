@extends('layouts.app')

@section('title', 'Developer Blogs')
@section('seo_title', 'Developer Blogs - FindDeveloper')
@section('seo_description', 'Read insights, tutorials, and stories from our developer community')
@section('seo_keywords', 'developer blogs, programming blog, tech blog, developer insights')

@section('content')
    @livewire('developer-blogs')
@endsection
