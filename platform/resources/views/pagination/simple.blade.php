@if($paginator->hasPages())
    <p style="margin-top:1rem">
        @if($paginator->onFirstPage())
            <span>Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">Previous</a>
        @endif
        <span> · page {{ $paginator->currentPage() }} · </span>
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next</a>
        @else
            <span>Next</span>
        @endif
    </p>
@endif
