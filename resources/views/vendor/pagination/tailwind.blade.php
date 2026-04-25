@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $pageLinks = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
            ->filter(fn (int $page) => $page >= 1 && $page <= $paginator->lastPage())
            ->unique()
            ->sort()
            ->values();

        $previousRenderedPage = null;
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="products-pagination-nav">
        <ul class="products-pagination-list">
            @if ($paginator->onFirstPage())
                <li class="products-pagination-item" aria-disabled="true">
                    <span class="products-pagination-link is-arrow is-disabled" aria-label="Pagina anterior">&lt;</span>
                </li>
            @else
                <li class="products-pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="products-pagination-link is-arrow" aria-label="Pagina anterior">&lt;</a>
                </li>
            @endif

            @foreach ($pageLinks as $page)
                @if ($previousRenderedPage !== null && $page - $previousRenderedPage > 1)
                    <li class="products-pagination-item" aria-disabled="true">
                        <span class="products-pagination-link is-disabled is-ellipsis">...</span>
                    </li>
                @endif

                @if ($page === $paginator->currentPage())
                    <li class="products-pagination-item" aria-current="page">
                        <span class="products-pagination-link is-current">{{ $page }}</span>
                    </li>
                @else
                    <li class="products-pagination-item">
                        <a href="{{ $paginator->url($page) }}" class="products-pagination-link">{{ $page }}</a>
                    </li>
                @endif

                @php
                    $previousRenderedPage = $page;
                @endphp
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="products-pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="products-pagination-link is-arrow" aria-label="Pagina siguiente">&gt;</a>
                </li>
            @else
                <li class="products-pagination-item" aria-disabled="true">
                    <span class="products-pagination-link is-arrow is-disabled" aria-label="Pagina siguiente">&gt;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
