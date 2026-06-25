@extends('layouts.app')
@section('title', 'Account Created')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Admin Panel
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link active"><i class="fas fa-users"></i> Graduates</a>
            <a href="{{ route('admin.graduates.create') }}" class="admin-nav-link"><i class="fas fa-user-plus"></i> Add Graduate</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div style="max-width:600px;margin:0 auto">

            {{-- Success Header --}}
            <div style="text-align:center;margin-bottom:2rem">
                <div style="font-size:3.5rem;margin-bottom:1rem">✅</div>
                <h1 style="font-size:1.8rem;margin-bottom:.4rem">Account Created!</h1>
                <p style="color:var(--text-muted)">
                    The portfolio account for <strong>{{ session('new_graduate_name') }}</strong> is ready.
                </p>
            </div>

            {{-- Credentials Card --}}
            <div class="admin-table-card" style="padding:2rem;margin-bottom:1.5rem">
                <h3 style="margin-bottom:1.5rem;display:flex;align-items:center;gap:.6rem">
                    <i class="fas fa-key" style="color:var(--primary)"></i>
                    Login Credentials
                </h3>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    {{-- Email --}}
                    <div style="background:rgba(196,120,58,.06);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem 1.25rem">
                        <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem">
                            <i class="fas fa-envelope"></i> Login Email
                        </div>
                        <div style="font-size:1.05rem;font-weight:600;font-family:monospace;color:var(--text);display:flex;justify-content:space-between;align-items:center">
                            <span id="cred-email">{{ session('new_graduate_email') }}</span>
                            <button onclick="copyText('cred-email', this)" class="btn-ghost-sm" id="copy-email-btn">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div style="background:rgba(196,120,58,.06);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem 1.25rem">
                        <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem">
                            <i class="fas fa-lock"></i> Temporary Password
                        </div>
                        <div style="font-size:1.2rem;font-weight:700;font-family:monospace;color:var(--primary);display:flex;justify-content:space-between;align-items:center;letter-spacing:.1em">
                            <span id="cred-password">{{ session('new_graduate_password') }}</span>
                            <button onclick="copyText('cred-password', this)" class="btn-ghost-sm" id="copy-pass-btn">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    {{-- Login URL --}}
                    <div style="background:rgba(196,120,58,.06);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem 1.25rem">
                        <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem">
                            <i class="fas fa-link"></i> Login URL
                        </div>
                        <div style="font-size:.95rem;font-family:monospace;color:var(--text);display:flex;justify-content:space-between;align-items:center">
                            <span id="cred-url">{{ url('/login') }}</span>
                            <button onclick="copyText('cred-url', this)" class="btn-ghost-sm" id="copy-url-btn">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Copy All Button --}}
                <button onclick="copyAll()" class="btn-primary btn-full" style="margin-top:1.5rem" id="copy-all-btn">
                    <i class="fas fa-clipboard"></i> Copy All Credentials
                </button>
            </div>

            {{-- Warning --}}
            <div class="alert alert-error" style="margin-bottom:1.5rem">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Save these credentials now!</strong><br>
                    <small>The password will NOT be shown again after you leave this page.</small>
                </div>
            </div>

            {{-- Note --}}
            <div class="alert alert-success" style="margin-bottom:1.5rem">
                <i class="fas fa-info-circle"></i>
                <div>
                    The graduate will be asked to <strong>change their password</strong> on first login.
                    You can send them the credentials via WhatsApp, email, or any other method.
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:1rem;justify-content:center">
                <a href="{{ route('admin.graduates.create') }}" class="btn-secondary" id="add-another-btn">
                    <i class="fas fa-user-plus"></i> Add Another Graduate
                </a>
                <a href="{{ route('admin.graduates.index') }}" class="btn-primary" id="back-to-list-btn">
                    <i class="fas fa-users"></i> View All Graduates
                </a>
            </div>

        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
function copyText(id, btn) {
    const text = document.getElementById(id).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.style.color = 'var(--success)';
        setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
    });
}

function copyAll() {
    const name     = '{{ session("new_graduate_name") }}';
    const email    = document.getElementById('cred-email').textContent.trim();
    const password = document.getElementById('cred-password').textContent.trim();
    const url      = document.getElementById('cred-url').textContent.trim();

    const text = `🎓 GradFolio Portfolio Access\n\nHello ${name}!\n\n📧 Email: ${email}\n🔑 Password: ${password}\n🔗 Login: ${url}\n\n⚠️ Please change your password after first login.`;

    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copy-all-btn');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied to Clipboard!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-clipboard"></i> Copy All Credentials';
        }, 3000);
    });
}
</script>
@endsection
