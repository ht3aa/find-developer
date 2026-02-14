@props([
    'promptText' => '',
    'searchUrl' => '',
    'hasFilters' => false,
])
<div class="filter-panel-ai-prompt-section">
    <div class="filter-panel-ai-prompt-heading-row">
        <span class="filter-panel-ai-prompt-heading-icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
        </span>
        <h4 class="filter-panel-ai-prompt-heading">AI Prompt</h4>
    </div>
    <p class="filter-panel-ai-prompt-desc">
        Copy this prompt to ask an AI assistant to search for developers on find-developer.com
        @if($hasFilters)
            using your current filter criteria.
        @else
            Add filters above, then copy the prompt below.
        @endif
    </p>
    <div class="filter-panel-ai-prompt-card">
        <div class="filter-panel-ai-prompt-card-header">
            <span class="filter-panel-ai-prompt-card-label">Prompt to copy</span>
            <button
                type="button"
                class="filter-panel-ai-prompt-copy-btn"
                x-data="{ copied: false }"
                @click="
                    navigator.clipboard.writeText(document.getElementById('ai-prompt-text').innerText);
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
            >
                <svg class="filter-panel-ai-prompt-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0a2 2 0 012 2v2m0 8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2h2" />
                </svg>
                <span x-text="copied ? 'Copied!' : 'Copy prompt'">Copy prompt</span>
            </button>
        </div>
        <pre class="filter-panel-ai-prompt-text" id="ai-prompt-text">{{ $promptText }}</pre>
    </div>
    @if($hasFilters)
        <div class="filter-panel-ai-prompt-url-box">
            <span class="filter-panel-ai-prompt-url-label">Direct link with your filters:</span>
            <a href="{{ $searchUrl }}" target="_blank" rel="noopener noreferrer" class="filter-panel-ai-prompt-url-link">{{ $searchUrl }}</a>
            <button
                type="button"
                class="filter-panel-ai-prompt-copy-url-btn"
                x-data="{ copied: false }"
                data-copy-url="{{ $searchUrl }}"
                @click="
                    navigator.clipboard.writeText($el.getAttribute('data-copy-url'));
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0a2 2 0 012 2v2m0 8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2h2" />
                </svg>
                <span x-text="copied ? 'Copied!' : 'Copy URL'">Copy URL</span>
            </button>
        </div>
    @endif
</div>
