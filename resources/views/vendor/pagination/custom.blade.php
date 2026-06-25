@if ($paginator->hasPages())
    <nav class="pagination-nav">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link">&laquo; Previous</span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev">&laquo; Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item disabled" aria-disabled="true"><span class="pagination-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active" aria-current="page"><span class="pagination-link">{{ $page }}</span></li>
                        @else
                            <li class="pagination-item"><a href="{{ $url }}" class="pagination-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next">Next &raquo;</a>
                </li>
            @else
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link">Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

<style>
.pagination-nav {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}
.pagination-list {
    display: flex;
    list-style: none;
    gap: 0.5rem;
    padding: 0;
}
.pagination-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0 0.75rem;
    border-radius: var(--radius-md);
    background: var(--surface-color);
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-weight: 500;
    transition: all var(--transition-fast);
}
.pagination-item:not(.disabled):not(.active) .pagination-link:hover {
    background: var(--surface-color-light);
    color: var(--text-primary);
    border-color: var(--primary-color);
}
.pagination-item.active .pagination-link {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}
.pagination-item.disabled .pagination-link {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
