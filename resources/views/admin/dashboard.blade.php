@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<main class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo">Mills IP</a>
        <nav class="admin-nav" aria-label="Admin navigation">
            <a class="active" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>Applications</span>
            <span>Notes</span>
            <span>Audit History</span>
        </nav>
    </aside>

    <section class="admin-main">
        <header class="admin-topbar">
            <div>
                <span class="admin-eyebrow">Admin Workspace</span>
                <h1>Phase 1 Foundation</h1>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="admin-logout-btn">Sign Out</button>
            </form>
        </header>

        <div class="admin-welcome">
            <div>
                <h2>Welcome, {{ Auth::guard('admin')->user()->name }}</h2>
                <p>The secure Mills IP admin area is active. Application management, notes, status updates, and audit history will build on this foundation.</p>
            </div>
            <span class="status-pill">Authenticated</span>
        </div>

        <div class="admin-metrics">
            <article>
                <span>Auth Guard</span>
                <strong>admin</strong>
                <p>Dedicated Mills IP team session guard.</p>
            </article>
            <article>
                <span>Database</span>
                <strong>Ready</strong>
                <p>Core tables, relationships, and indexes are migrated.</p>
            </article>
            <article>
                <span>API Search</span>
                <strong>Live</strong>
                <p>IP Australia OAuth search integration is connected.</p>
            </article>
        </div>
    </section>
</main>

@endsection
