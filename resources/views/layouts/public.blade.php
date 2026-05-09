<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Search the official IP Australia trademark database in real time. Mills IP Australian trademark attorneys.')">
    <title>@yield('title', 'Mills IP') | Australian Trademark Attorneys</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<nav class="site-nav" role="navigation" aria-label="Main navigation">
    <div class="container nav-inner">
        <a href="{{ route('search') }}" class="nav-logo">Mills IP</a>
        <div class="nav-links">
            <a href="{{ route('search') }}" class="nav-link">Trademark Search</a>
            <a href="{{ route('search') }}#search" class="btn btn-primary">Search Now</a>
        </div>
    </div>
</nav>

<main role="main">
    @yield('content')
</main>

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('search') }}" class="footer-logo">Mills IP</a>
                <p class="footer-tagline">Australian trademark search and attorney review.</p>
            </div>
            <div>
                <p class="footer-col-heading">Platform</p>
                <ul class="footer-links">
                    <li><a href="{{ route('search') }}">Trademark Search</a></li>
                    <li><a href="{{ route('search') }}#process">Application Process</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-col-heading">Admin</p>
                <ul class="footer-links">
                    <li><a href="{{ route('admin.login') }}">Team Login</a></li>
                    <li><a href="{{ route('search') }}">Public Search</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copy">&copy; {{ date('Y') }} Mills IP Pty Ltd. All rights reserved.</p>
            <span class="footer-badge">Australian Trademark Attorneys</span>
        </div>
    </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
