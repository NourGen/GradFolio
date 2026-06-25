<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="GradFolio — Discover talented graduates and their portfolios">
    @endif
    <title>@yield('title', 'GradFolio') — Graduate Portfolio Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- BSA Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>

<nav class="navbar" id="main-nav">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="nav-brand">
            <span class="brand-icon">🎓</span>
            <span class="brand-text">GradFolio</span>
        </a>
        <div class="nav-links" id="nav-links">
            <a href="{{ route('directory') }}" class="nav-link">Directory</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin Panel</a>
                @else
                    <a href="{{ route('dashboard') }}" class="nav-link">My Portfolio</a>
                    @if(auth()->user()->portfolio)
                        <a href="{{ route('dashboard.analytics') }}" class="nav-link">Analytics</a>
                    @endif
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link">Login</a>
            @endauth
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle light/dark mode">
                <span class="theme-icon">☀️</span>
            </button>
        </div>
        <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<main class="main-content">


    @if(session('success'))
        <div class="alert alert-success" id="flash-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" id="flash-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('status'))
        <div class="alert alert-success" id="flash-status">
            <i class="fas fa-info-circle"></i> {{ session('status') }}
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <span style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;">🎓 GradFolio</span>
            <p>Showcasing tomorrow's talent, today.</p>
        </div>
        <div class="footer-links">
            <a href="{{ route('directory') }}">Browse Graduates</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} GradFolio — <a href="https://b-s-a.co/ar/" target="_blank" style="color: inherit; text-decoration: underline;">BSA Academy</a>. Built by NourGen</p>
    </div>
</footer>

<script>
    // ── Theme Toggle ──────────────────────────────────────────
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon   = themeToggle?.querySelector('.theme-icon');

    const applyTheme = (isDark) => {
        document.body.classList.toggle('light-mode', !isDark);
        if (themeIcon) themeIcon.textContent = isDark ? '☀️' : '🌙';
        localStorage.setItem('bsa-theme', isDark ? 'dark' : 'light');
    };

    // Restore saved theme or use system preference
    const savedTheme   = localStorage.getItem('bsa-theme');
    const prefersDark  = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(savedTheme ? savedTheme === 'dark' : prefersDark);

    themeToggle?.addEventListener('click', () => {
        applyTheme(document.body.classList.contains('light-mode'));
    });

    // ── Mobile nav toggle ─────────────────────────────────────
    document.getElementById('nav-toggle')?.addEventListener('click', () => {
        document.getElementById('nav-links').classList.toggle('active');
    });

    // ── Auto-dismiss flash alerts ─────────────────────────────
    ['flash-success','flash-error','flash-status'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 450);
        }, 4500);
    });

    // ── Navbar scroll effect ──────────────────────────────────
    window.addEventListener('scroll', () => {
        document.getElementById('main-nav').classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
</script>
@yield('scripts')
</body>
</html>
