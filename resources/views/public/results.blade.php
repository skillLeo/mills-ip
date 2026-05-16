@extends('layouts.public')

@section('title', 'Results for "' . $query . '" — Trademark Search')
@section('meta_description', 'Trademark search results for "' . $query . '" in the official IP Australia database.')

@section('content')

<div class="results-page">

    {{-- ─── Header ─── --}}
    <div class="rp-header">
        <div class="container">
            <a href="{{ route('search') }}" class="rp-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                New search
            </a>
            <div class="rp-header-row">
                <div class="rp-header-left">
                    <div class="rp-live-badge">
                        <span class="live-dot"></span>
                        IP Australia Live Database
                    </div>
                    <h1>Results for <em>"{{ $query }}"</em></h1>
                    @if(!isset($apiError) || !$apiError)
                        <p>{{ $total ?? count($results) }} {{ Str::plural('record', $total ?? count($results)) }} found</p>
                    @endif
                </div>
                <form action="{{ route('search.results') }}" method="GET" class="rp-search" role="search">
                    <span class="rp-search-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $query }}" aria-label="Search another trademark" maxlength="100" required placeholder="Search another name...">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── Body ─── --}}
    <div class="rp-body">
        <div class="container">

            @if(isset($apiError) && $apiError)
                <div class="rp-state-card">
                    <div class="rp-state-icon rp-state-icon--warn">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <h2>Search temporarily unavailable</h2>
                    <p>We could not connect to the IP Australia database. This is a temporary issue — please try your search again shortly.</p>
                    <a href="{{ route('search') }}" class="btn-solid">Try Again</a>
                </div>

            @elseif(count($results) > 0)

                <div class="rp-meta-bar">
                    <div class="rp-meta-left">
                        <span class="rp-meta-total">{{ $total ?? count($results) }} records found</span>
                    </div>
                    <div class="rp-meta-right">
                        <div class="rp-meta-key-item">
                            <span class="badge badge-green">Registered</span>
                            <span class="rp-badge-label">Active conflict</span>
                        </div>
                        <div class="rp-meta-key-item">
                            <span class="badge badge-orange">Pending</span>
                            <span class="rp-badge-label">In progress</span>
                        </div>
                        <div class="rp-meta-key-item">
                            <span class="badge badge-red">Lapsed</span>
                            <span class="rp-badge-label">No longer protected</span>
                        </div>
                    </div>
                </div>

                <div class="rp-results rp-results--grid" id="rp-results-grid">
                    @foreach($results as $tm)
                        @include('public._tm_card', ['tm' => $tm])
                    @endforeach

                    @if($hasMore)
                    <div class="rp-load-more" id="rp-load-more-wrap">
                        <button class="rp-load-more-btn" id="rp-load-more">
                            Load More Results
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <span class="rp-load-more-count">Showing <strong id="rp-shown">{{ $loaded }}</strong> of <strong id="rp-total">{{ $total }}</strong></span>
                    </div>
                    @endif

                    <div class="rp-apply-cta">
                        <div class="rp-apply-left">
                            <h3>Ready to register <em>"{{ $query }}"</em>?</h3>
                            <p>A Mills IP attorney will review your application and provide a fixed fee quote within one business day. No payment required to apply.</p>
                        </div>
                        <a href="{{ route('apply.step1') }}?brand={{ urlencode($query) }}" class="btn-solid-white">
                            Apply for "{{ Str::limit($query, 20) }}"
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>

            @else
                <div class="rp-state-card">
                    <div class="rp-state-icon rp-state-icon--ok">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h2>No matching records found</h2>
                    <p>Your search for <strong>"{{ $query }}"</strong> returned no results in the live trademark database. This is a promising sign — but a Mills IP attorney should still verify availability before you file.</p>
                    <div class="rp-state-actions">
                        <a href="{{ route('search') }}" class="btn-outline">Search Another Name</a>
                        <a href="{{ route('apply.step1') }}?brand={{ urlencode($query) }}" class="btn-solid">
                            Apply for "{{ $query }}"
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    var btn    = document.getElementById('rp-load-more');
    var wrap   = document.getElementById('rp-load-more-wrap');
    var grid   = document.getElementById('rp-results-grid');
    var shownEl = document.getElementById('rp-shown');
    var query  = {{ json_encode($query) }};
    var page   = 2;
    var loading = false;

    if (!btn) return;

    btn.addEventListener('click', function () {
        if (loading) return;
        loading = true;
        btn.disabled = true;
        btn.innerHTML = 'Loading... <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="rp-spin"><polyline points="6 9 12 15 18 9"></polyline></svg>';

        fetch('/search/more?q=' + encodeURIComponent(query) + '&page=' + page, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var temp = document.createElement('div');
            temp.innerHTML = data.html;
            while (temp.firstChild) {
                grid.insertBefore(temp.firstChild, wrap);
            }
            if (shownEl) shownEl.textContent = data.loaded;
            page++;
            loading = false;
            btn.disabled = false;

            if (!data.hasMore) {
                wrap.style.display = 'none';
            } else {
                btn.innerHTML = 'Load More Results <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            }
        })
        .catch(function () {
            loading = false;
            btn.disabled = false;
            btn.innerHTML = 'Load More Results <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>';
        });
    });
})();
</script>
@endpush

@endsection
