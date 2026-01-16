@if ($paginator->hasPages())
    <div class="pagination-modern" wire:key="pagination-{{ $paginator->currentPage() }}">
        {{-- Pagination Info --}}
        <div class="pagination-info">
            <span class="pagination-text">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </span>
        </div>

        {{-- Pagination Links --}}
        <ul class="pagination-links">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-link-disabled">
                        <svg class="pagination-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <button type="button" wire:click="previousPage('page')" class="pagination-link" rel="prev">
                        <svg class="pagination-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </button>
                </li>
            @endif



            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <button type="button" wire:click="nextPage('page')" class="pagination-link" rel="next">
                        Next
                        <svg class="pagination-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </li>
            @else
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-link-disabled">
                        Next
                        <svg class="pagination-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </div>
@endif
