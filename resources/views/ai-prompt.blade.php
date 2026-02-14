@extends('layouts.app')

@section('title', 'AI Prompt')
@section('seo_title', 'AI Prompt - Find Developer | Copy prompt to search developers')
@section('seo_description', 'Generate an AI prompt to search for developers on Find Developer with your filters. Copy the prompt and URL for AI assistants.')
@section('seo_keywords', 'AI prompt, find developer, developer search, copy prompt, search developers')

@section('content')
    @livewire('ai-prompt')
@endsection
