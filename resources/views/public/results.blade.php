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
                        @if(($total ?? count($results)) > count($results))
                            <p>Showing {{ count($results) }} of {{ $total }} records found — refine your search to narrow results.</p>
                        @else
                            <p>{{ count($results) }} {{ Str::plural('record', count($results)) }} found matching your search.</p>
                        @endif
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
                <div class="rp-layout">

                    <aside class="rp-sidebar">
                        <div class="rp-summary-card">
                            <div class="rp-summary-label">Search Results</div>
                            <div class="rp-summary-count">{{ $total ?? count($results) }}</div>
                            <p>Live matching records for <strong>"{{ $query }}"</strong> from the IP Australia trademark register.</p>
                        </div>
                        <div class="rp-explain-card">
                            <h4>What these results mean</h4>
                            <ul class="rp-explain-list">
                                <li>
                                    <span class="badge badge-green">Registered</span>
                                    <span>Active — potential conflict</span>
                                </li>
                                <li>
                                    <span class="badge badge-orange">Pending</span>
                                    <span>Application in progress</span>
                                </li>
                                <li>
                                    <span class="badge badge-red">Lapsed / Refused</span>
                                    <span>No longer protected</span>
                                </li>
                            </ul>
                            <p class="rp-explain-note">Always consult an attorney before filing — similarity can still cause conflicts even when names differ slightly.</p>
                        </div>
                    </aside>

                    <div class="rp-results">
                        @foreach($results as $tm)
                            @php
                                $statusKey = strtoupper($tm['status'] ?? '');
                                $badgeClass = match($statusKey) {
                                    'REGISTERED'       => 'badge-green',
                                    'PENDING'          => 'badge-orange',
                                    'NEVER_REGISTERED',
                                    'REFUSED',
                                    'REMOVED'          => 'badge-red',
                                    default            => 'badge-dim',
                                };
                                $statusLabel = match($statusKey) {
                                    'REGISTERED'       => 'Registered',
                                    'PENDING'          => 'Pending',
                                    'NEVER_REGISTERED' => 'Lapsed',
                                    'REFUSED'          => 'Refused',
                                    'REMOVED'          => 'Removed',
                                    default            => ucfirst(strtolower($statusKey)) ?: 'Unknown',
                                };
                            @endphp
                            <article class="tm-card">
                                <div class="tm-card-head">
                                    <div class="tm-name-row">
                                        <h2 class="tm-name">{{ $tm['trademark_name'] ?? '—' }}</h2>
                                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    @if(!empty($tm['owner']))
                                        <p class="tm-owner">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            {{ $tm['owner'] }}
                                        </p>
                                    @endif
                                </div>
                                <dl class="tm-facts">
                                    @if(!empty($tm['trademark_number']))
                                        <div class="tm-fact">
                                            <dt>Number</dt>
                                            <dd>{{ $tm['trademark_number'] }}</dd>
                                        </div>
                                    @endif
                                    @if(!empty($tm['class']))
                                        <div class="tm-fact">
                                            <dt>Class</dt>
                                            <dd>{{ $tm['class'] }}</dd>
                                        </div>
                                    @endif
                                    @if(!empty($tm['application_date']))
                                        <div class="tm-fact">
                                            <dt>Filed</dt>
                                            <dd>{{ $tm['application_date'] }}</dd>
                                        </div>
                                    @endif
                                    @if(!empty($tm['registration_date']))
                                        <div class="tm-fact">
                                            <dt>Registered</dt>
                                            <dd>{{ $tm['registration_date'] }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </article>
                        @endforeach

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

@endsection
