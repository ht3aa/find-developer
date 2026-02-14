<div class="modern-container ai-prompt-container">
    <div class="ai-prompt-header">
        <h1 class="ai-prompt-title">AI Prompt</h1>
        <p class="ai-prompt-subtitle">
            Copy this prompt to ask an AI assistant to search for developers on https://find-developer.com
            @if($hasFilters)
                using your current filter criteria.
            @else
                You can add filters on the <a href="{{ url('/') }}">search page</a> and then return here to get a prompt with those requirements.
            @endif
        </p>
        <a href="{{ url('/') }}{{ $hasFilters ? '?' . request()->getQueryString() : '' }}" class="ai-prompt-back-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to search
        </a>
    </div>

    <div class="ai-prompt-card">
        <div class="ai-prompt-card-header">
            <span class="ai-prompt-card-label">Prompt to copy</span>
            <button
                type="button"
                class="ai-prompt-copy-btn"
                x-data="{ copied: false }"
                @click="
                    navigator.clipboard.writeText(document.getElementById('ai-prompt-text').innerText);
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
            >
                <svg class="ai-prompt-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0a2 2 0 012 2v2m0 8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2h2" />
                </svg>
                <span x-text="copied ? 'Copied!' : 'Copy prompt'">Copy prompt</span>
            </button>
        </div>
        <pre class="ai-prompt-text" id="ai-prompt-text">{{ $promptText }}</pre>
    </div>

    @if($hasFilters)
        <div class="ai-prompt-url-box">
            <span class="ai-prompt-url-label">Direct link with your filters:</span>
            <a href="{{ $searchUrl }}" target="_blank" rel="noopener noreferrer" class="ai-prompt-url-link">{{ $searchUrl }}</a>
            <button
                type="button"
                class="ai-prompt-copy-url-btn"
                x-data="{ copied: false }"
                @click="
                    navigator.clipboard.writeText('{{ $searchUrl }}');
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0a2 2 0 012 2v2m0 8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2h2" />
                </svg>
                <span x-text="copied ? 'Copied!' : 'Copy URL'">Copy URL</span>
            </button>
        </div>
    @endif

    <style>
    .ai-prompt-container { max-width: 800px; margin: 0 auto; padding: var(--spacing-lg, 1.5rem); }
    .ai-prompt-header { margin-bottom: var(--spacing-xl, 2rem); }
    .ai-prompt-title { font-size: var(--font-size-2xl, 1.5rem); font-weight: 700; color: var(--text-primary); margin: 0 0 var(--spacing-sm); }
    .ai-prompt-subtitle { color: var(--text-secondary); font-size: var(--font-size-base); margin: 0 0 var(--spacing-md); line-height: 1.5; }
    .ai-prompt-subtitle a { color: var(--color-primary); text-decoration: none; }
    .ai-prompt-subtitle a:hover { text-decoration: underline; }
    .ai-prompt-back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-primary); text-decoration: none; font-size: var(--font-size-sm); }
    .ai-prompt-back-link:hover { text-decoration: underline; }
    .ai-prompt-card { background: var(--bg-primary); border: 1px solid var(--border-primary); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); overflow: hidden; }
    .ai-prompt-card-header { display: flex; align-items: center; justify-content: space-between; padding: var(--spacing-md) var(--spacing-lg); background: var(--bg-secondary); border-bottom: 1px solid var(--border-primary); }
    .ai-prompt-card-label { font-size: var(--font-size-sm); font-weight: 600; color: var(--text-secondary); }
    .ai-prompt-copy-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: var(--font-size-sm); font-weight: 500; color: var(--color-primary); background: transparent; border: 1px solid var(--color-primary); border-radius: var(--radius-md); cursor: pointer; transition: var(--transition-base); }
    .ai-prompt-copy-btn:hover { background: var(--color-primary); color: white; }
    .ai-prompt-copy-icon { width: 18px; height: 18px; }
    .ai-prompt-text { margin: 0; padding: var(--spacing-lg); font-family: ui-monospace, monospace; font-size: var(--font-size-sm); line-height: 1.6; color: var(--text-primary); white-space: pre-wrap; word-break: break-word; overflow-x: auto; }
    .ai-prompt-url-box { margin-top: var(--spacing-lg); padding: var(--spacing-md); background: var(--bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--border-primary); }
    .ai-prompt-url-label { display: block; font-size: var(--font-size-xs); font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem; }
    .ai-prompt-url-link { font-size: var(--font-size-sm); color: var(--color-primary); word-break: break-all; }
    .ai-prompt-copy-url-btn { display: inline-flex; align-items: center; gap: 0.35rem; margin-top: 0.5rem; padding: 0.35rem 0.75rem; font-size: var(--font-size-xs); color: var(--text-secondary); background: var(--bg-primary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); cursor: pointer; }
    .ai-prompt-copy-url-btn:hover { color: var(--color-primary); border-color: var(--color-primary); }
    </style>
</div>
