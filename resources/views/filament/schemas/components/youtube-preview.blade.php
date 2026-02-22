@php
    $youtubeUrl = $get('youtube_url');
    $videoId = null;
    if ($youtubeUrl && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $youtubeUrl, $matches)) {
        $videoId = $matches[1];
    }
@endphp
@if($videoId)
<div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
    <div class="aspect-video w-full">
        <iframe
            class="w-full h-full"
            src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1&mute=1"
            title="YouTube video preview"
            referrerpolicy="strict-origin-when-cross-origin"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
        ></iframe>
    </div>
</div>
@endif
