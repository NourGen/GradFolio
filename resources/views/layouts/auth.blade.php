<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GradFolio') — Graduate Portfolio Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">

<div class="auth-container">
    <!-- Left Panel — Brand -->
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ route('home') }}" class="auth-brand">
                <span>🎓</span> GradFolio
            </a>
            <h1 class="auth-tagline">Build Your<br><span class="gradient-text">Graduate Portfolio</span></h1>
            <p class="auth-sub">
                Create a stunning portfolio, showcase your projects, and get discovered by top employers — all in one place.
            </p>
            <div class="auth-features">
                <div class="auth-feature"><i class="fas fa-rocket"></i> <span>Publish your portfolio in minutes</span></div>
                <div class="auth-feature"><i class="fas fa-images"></i> <span>Showcase projects with images</span></div>
                <div class="auth-feature"><i class="fas fa-chart-line"></i> <span>Track who views your work</span></div>
                <div class="auth-feature"><i class="fas fa-file-pdf"></i> <span>Share your CV securely</span></div>
                <div class="auth-feature"><i class="fas fa-shield-alt"></i> <span>Privacy-first analytics</span></div>
            </div>
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="auth-right">
        <div class="auth-card">
            {{-- Flash Messages --}}
            @if(session('status'))
                <div class="alert alert-success" style="margin-bottom:1.5rem">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom:1.5rem">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('auth-content')
        </div>
        <!-- Theme Toggle -->
        <button class="theme-toggle" id="theme-toggle" style="position:absolute;top:1.5rem;right:1.5rem" aria-label="Toggle theme">
            <span class="theme-icon">☀️</span>
        </button>
    </div>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon   = themeToggle?.querySelector('.theme-icon');
    const applyTheme = (isDark) => {
        document.body.classList.toggle('light-mode', !isDark);
        if (themeIcon) themeIcon.textContent = isDark ? '☀️' : '🌙';
        localStorage.setItem('bsa-theme', isDark ? 'dark' : 'light');
    };
    const saved      = localStorage.getItem('bsa-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(saved ? saved === 'dark' : prefersDark);
    themeToggle?.addEventListener('click', () => {
        applyTheme(document.body.classList.contains('light-mode'));
    });
</script>
</body>
</html>
