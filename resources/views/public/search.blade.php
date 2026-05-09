@extends('layouts.public')

@section('title', 'Trademark Search')
@section('meta_description', 'Search the official IP Australia trademark database in real time. Free trademark search by Mills IP.')

@section('content')

<section class="hero" id="search">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow"><span></span> Live IP Australia trademark data</span>

            <h1>Search your trademark before you apply.</h1>

            <p class="hero-sub">
                Check matching Australian trademark records in seconds, then prepare the right next step with Mills IP.
            </p>

            <form action="{{ route('search.results') }}" method="GET" class="search-form" role="search">
                <span class="search-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.5-3.5"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    class="search-input"
                    placeholder="Enter a brand name, word, or phrase"
                    autocomplete="off"
                    spellcheck="false"
                    aria-label="Trademark search"
                    maxlength="100"
                    value="{{ old('q') }}"
                    required
                >
                <button type="submit" class="search-submit">Search</button>
            </form>

            <div class="trust-row" aria-label="Search benefits">
                <span>Official registry data</span>
                <span>OAuth-secured API</span>
                <span>No account required</span>
            </div>
        </div>

        <div class="hero-panel" aria-hidden="true">
            <div class="registry-card">
                <div class="registry-top">
                    <span>Trade Mark Record</span>
                    <strong>REGISTERED</strong>
                </div>
                <h2>NIKE</h2>
                <p>Nike Innovate C.V.</p>
                <dl>
                    <div>
                        <dt>Number</dt>
                        <dd>284351</dd>
                    </div>
                    <div>
                        <dt>Class</dt>
                        <dd>25</dd>
                    </div>
                    <div>
                        <dt>Filed</dt>
                        <dd>1975-01-08</dd>
                    </div>
                    <div>
                        <dt>Source</dt>
                        <dd>IP Australia</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>

<section class="proof-band">
    <div class="container proof-grid">
        <div>
            <strong>Live</strong>
            <span>Government API connection</span>
        </div>
        <div>
            <strong>OAuth 2.0</strong>
            <span>Client credential authentication</span>
        </div>
        <div>
            <strong>Admin Ready</strong>
            <span>Protected Mills IP team access</span>
        </div>
    </div>
</section>

<section class="process-section" id="process">
    <div class="container">
        <div class="section-heading">
            <span class="section-label">Process</span>
            <h2>Built for a clear trademark application journey</h2>
        </div>

        <div class="steps-outer">
            <article class="step-card">
                <span class="step-num">01</span>
                <h3>Search the registry</h3>
                <p>Users enter a proposed brand name and receive live matching records from IP Australia.</p>
            </article>
            <article class="step-card">
                <span class="step-num">02</span>
                <h3>Review potential conflicts</h3>
                <p>Results show number, owner, class, status, and key dates so the user can assess risk.</p>
            </article>
            <article class="step-card">
                <span class="step-num">03</span>
                <h3>Prepare the application</h3>
                <p>The platform foundation is ready for the guided application form in the next build phase.</p>
            </article>
        </div>
    </div>
</section>

@endsection
