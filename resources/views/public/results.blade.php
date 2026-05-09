@extends('layouts.public')

@section('title', 'Results for "' . $query . '"')
@section('meta_description', 'Trademark search results for "' . $query . '" in the IP Australia database.')

@section('content')

<section class="results-header">
    <div class="container">
        <a href="{{ route('search') }}" class="back-link">Back to search</a>
        <div class="results-title-row">
            <div>
                <span class="eyebrow"><span></span> IP Australia results</span>
                <h1>Results for "{{ $query }}"</h1>
                @if(!isset($apiError) || !$apiError)
                    <p>{{ count($results) }} {{ Str::plural('record', count($results)) }} found in the live trademark database.</p>
                @endif
            </div>
            <form action="{{ route('search.results') }}" method="GET" class="compact-search" role="search">
                <input type="text" name="q" value="{{ $query }}" aria-label="Search another trademark" maxlength="100" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
</section>

<section class="results-body">
    <div class="container">
        @if(isset($apiError) && $apiError)
            <div class="state-card">
                <span class="state-icon">!</span>
                <h2>Search temporarily unavailable</h2>
                <p>The platform could not connect to IP Australia. Please try another search shortly.</p>
                <a href="{{ route('search') }}" class="btn btn-primary">Try Again</a>
            </div>
        @elseif(count($results) > 0)
            <div class="results-layout">
                <aside class="results-summary">
                    <span class="summary-label">Search Summary</span>
                    <strong>{{ count($results) }}</strong>
                    <p>Live matching records returned for "{{ $query }}". Registered results may indicate possible conflicts.</p>
                </aside>

                <div class="results-list">
                    @foreach($results as $tm)
                        @php
                            $raw = strtolower($tm['status'] ?? '');
                            $badgeClass = match(true) {
                                str_contains($raw, 'register') => 'badge-green',
                                str_contains($raw, 'pend') => 'badge-orange',
                                str_contains($raw, 'reject') || str_contains($raw, 'expir') => 'badge-red',
                                default => 'badge-dim',
                            };
                            $statusLabel = strtoupper($tm['status'] ?? 'Unknown');
                        @endphp
                        <article class="result-card">
                            <div class="result-main">
                                <div class="result-title-line">
                                    <h2>{{ $tm['trademark_name'] ?? '-' }}</h2>
                                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </div>
                                @if(!empty($tm['owner']))
                                    <p class="result-owner">{{ $tm['owner'] }}</p>
                                @endif
                                <dl class="result-facts">
                                    @if(!empty($tm['trademark_number']))
                                        <div><dt>Number</dt><dd>{{ $tm['trademark_number'] }}</dd></div>
                                    @endif
                                    @if(!empty($tm['class']))
                                        <div><dt>Class</dt><dd>{{ $tm['class'] }}</dd></div>
                                    @endif
                                    @if(!empty($tm['application_date']))
                                        <div><dt>Filed</dt><dd>{{ $tm['application_date'] }}</dd></div>
                                    @endif
                                    @if(!empty($tm['registration_date']))
                                        <div><dt>Registered</dt><dd>{{ $tm['registration_date'] }}</dd></div>
                                    @endif
                                </dl>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <div class="state-card">
                <span class="state-icon">0</span>
                <h2>No matching records found</h2>
                <p>Your search for "{{ $query }}" returned no live records. A trademark attorney should still review availability before filing.</p>
                <a href="{{ route('search') }}" class="btn btn-primary">Search Another Name</a>
            </div>
        @endif
    </div>
</section>

@endsection
