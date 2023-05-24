@if ($paginator->hasPages())
    <ul class="kt-pagination__links">
        <li class="kt-pagination__link--first">
            <a href="{{ $paginator->url(1) }}"><i class="fa fa-angle-double-left kt-font-danger"></i></a>
        </li>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="kt-pagination__link--next disabled" aria-disabled="true"
                aria-label="@lang('pagination.previous')">
                <a class="disabled"><i class="fa fa-angle-left kt-font-danger"></i></a>
            </li>
        @else
            <li class="kt-pagination__link--next">
                <a class="" href=" {{ $paginator->previousPageUrl() }}" rel="prev"
                    aria-label="@lang('pagination.previous')">
                    <i class="fa fa-angle-left kt-font-danger"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true"><span
                        class="">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="
                        kt-pagination__link--active" aria-current="page"><a
                            href="javascript:void(0);">{{ $page }}</a></li>
            @else
                <li class=""><a class="" href=" {{ $url }}">{{ $page }}</a></li>
            @endif
        @endforeach
@endif
@endforeach

{{-- Next Page Link --}}
@if ($paginator->hasMorePages())
    <li class="kt-pagination__link--prev">
        <a class="" href=" {{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
            <i class="fa fa-angle-right kt-font-danger"></i>
        </a>
    </li>
@else
    <li class="kt-pagination__link--prev disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
        <a class=""><i class=" fa fa-angle-right kt-font-danger"></i></a>
    </li>
@endif
<li class="kt-pagination__link--last">
    <a href="{{ $paginator->url($paginator->lastPage()) }}"><i
            class="fa fa-angle-double-right kt-font-danger"></i></a>
</li>
</ul>
@endif
