@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="dashboard-layout">

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                @if($portfolio->profilePictureUrl())
                    <img src="{{ $portfolio->profilePictureUrl() }}" alt="Profile">
                @else
                    <div class="avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
            </div>
            <h3>{{ auth()->user()->name }}</h3>
            <p class="sidebar-headline">{{ $portfolio->headline ?? 'Graduate' }}</p>
            <span class="publish-badge {{ $portfolio->is_published ? 'badge-live' : 'badge-draft' }}">
                {{ $portfolio->is_published ? '🟢 Live' : '⚪ Draft' }}
            </span>
        </div>

        <nav class="sidebar-nav">
            <a href="#section-personal"  class="sidebar-link dash-nav-link" id="nav-personal"><i class="fas fa-user"></i> Personal Info</a>
            <a href="#section-skills"    class="sidebar-link dash-nav-link" id="nav-skills"><i class="fas fa-star"></i> Skills</a>
            <a href="#section-contact"   class="sidebar-link dash-nav-link" id="nav-contact"><i class="fas fa-address-card"></i> Contact</a>
            <a href="#section-cv"        class="sidebar-link dash-nav-link" id="nav-cv"><i class="fas fa-file-pdf"></i> CV</a>
            <a href="#section-projects"  class="sidebar-link dash-nav-link" id="nav-projects"><i class="fas fa-code"></i> Projects</a>
            <a href="#section-password"  class="sidebar-link dash-nav-link" id="nav-password"><i class="fas fa-lock"></i> Password</a>
            <a href="{{ route('dashboard.analytics') }}" class="sidebar-link"><i class="fas fa-chart-line"></i> Analytics</a>
            @if($portfolio->slug && $portfolio->is_published)
            <a href="{{ route('portfolio.show', $portfolio->slug) }}" target="_blank" class="sidebar-link">
                <i class="fas fa-eye"></i> Preview
            </a>
            @endif
        </nav>

        <form method="POST" action="{{ route('dashboard.portfolio.toggle') }}" class="publish-form">
            @csrf
            <button type="submit" class="btn-publish {{ $portfolio->is_published ? 'btn-unpublish' : 'btn-go-live' }}" id="toggle-publish-btn">
                @if($portfolio->is_published)
                    <i class="fas fa-eye-slash"></i> Unpublish
                @else
                    <i class="fas fa-rocket"></i> Go Live!
                @endif
            </button>
        </form>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────── --}}
    <main class="dashboard-main">

        {{-- ═══ 1. PERSONAL INFORMATION ════════════════════════ --}}
        <div class="dashboard-card" id="section-personal">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
            </div>

            {{-- Profile Picture --}}
            <div class="dash-picture-row">
                <div class="dash-picture-preview">
                    @if($portfolio->profilePictureUrl())
                        <img src="{{ $portfolio->profilePictureUrl() }}"
                             alt="Profile" class="dash-avatar-img" id="avatar-preview-img">
                    @else
                        <div class="dash-avatar-placeholder" id="avatar-preview-placeholder">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="dash-picture-upload">
                    <form method="POST" action="{{ route('dashboard.portfolio.picture') }}"
                          enctype="multipart/form-data" id="picture-form">
                        @csrf
                        <label for="profile_picture" class="file-drop-zone file-drop-compact" id="picture-drop-zone">
                            <i class="fas fa-camera"></i>
                            <span>Upload new photo</span>
                            <small>JPG, PNG, WebP · max 2MB</small>
                            <input type="file" name="profile_picture" id="profile_picture"
                                   accept="image/*" style="display:none"
                                   onchange="previewAvatar(this)">
                        </label>
                        @error('profile_picture')<span class="form-error">{{ $message }}</span>@enderror
                        <button type="submit" class="btn-secondary btn-sm" id="upload-pic-btn">
                            <i class="fas fa-upload"></i> Save Photo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info Form --}}
            <form method="POST" action="{{ route('dashboard.personal.update') }}" class="dash-form" id="personal-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Full Name / Display Name</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $portfolio->title) }}"
                               placeholder="e.g. Sarah Al-Hassan">
                        @error('title')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Professional Titles (Max 3)</label>
                        @php
                            $titles = array_filter(array_map('trim', explode(',', $portfolio->headline ?? '')));
                            $title1 = $titles[0] ?? '';
                            $title2 = $titles[1] ?? '';
                            $title3 = $titles[2] ?? '';
                        @endphp
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;" id="headline-fields">
                            <input type="text" name="headline[]" id="headline-1"
                                   value="{{ old('headline.0', $title1) }}"
                                   placeholder="First Title (e.g. Full-Stack Developer)">
                            <input type="text" name="headline[]" id="headline-2"
                                   value="{{ old('headline.1', $title2) }}"
                                   placeholder="Second Title (Optional, e.g. UI/UX Designer)">
                            <input type="text" name="headline[]" id="headline-3"
                                   value="{{ old('headline.2', $title3) }}"
                                   placeholder="Third Title (Optional, e.g. Mobile Developer)">
                        </div>
                        @error('headline')<span class="form-error">{{ $message }}</span>@enderror
                        @error('headline.*')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $portfolio->location) }}"
                           placeholder="e.g. Dubai, UAE">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_prefix">Hero Prefix Text</label>
                        <input type="text" name="hero_prefix" id="hero_prefix"
                               value="{{ old('hero_prefix', $portfolio->hero_prefix) }}"
                               placeholder="e.g. A results-driven">
                        @error('hero_prefix')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="hero_suffix">Hero Suffix Text</label>
                        <input type="text" name="hero_suffix" id="hero_suffix"
                               value="{{ old('hero_suffix', $portfolio->hero_suffix) }}"
                               placeholder="e.g. passionate about building products...">
                        @error('hero_suffix')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="track">Track</label>
                        <input type="text" name="track" id="track"
                               value="{{ old('track', $portfolio->track) }}"
                               placeholder="e.g. Full-Stack Development">
                        @error('track')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" name="specialization" id="specialization"
                               value="{{ old('specialization', $portfolio->specialization) }}"
                               placeholder="e.g. Laravel & Vue, SEO">
                        @error('specialization')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="graduation_year">Graduation Year</label>
                        <select name="graduation_year" id="graduation_year" style="width: 100%; padding: 0.75rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-md); color: var(--text);">
                            <option value="">— Select Year —</option>
                            @for($y = date('Y') + 4; $y >= 2025; $y--)
                                <option value="{{ $y }}" {{ old('graduation_year', $portfolio->graduation_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        @error('graduation_year')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="bio">About Me</label>
                    <textarea name="bio" id="bio" rows="5"
                              placeholder="Tell employers about yourself — your background, passions, and goals...">{{ old('bio', $portfolio->bio) }}</textarea>
                    <small class="char-count" id="bio-count">{{ strlen($portfolio->bio ?? '') }} / 2000</small>
                    @error('bio')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn-primary" id="save-personal-btn">
                    <i class="fas fa-save"></i> Save Personal Info
                </button>
            </form>
        </div>

        {{-- ═══ 2. SKILLS ══════════════════════════════════════ --}}
        <div class="dashboard-card" id="section-skills">
            <div class="card-header">
                <h2><i class="fas fa-star"></i> Skills</h2>
                <span class="card-count">{{ $portfolio->skills->count() }} / 30</span>
            </div>

            {{-- Existing Skills --}}
            @if($portfolio->skills->count())
            <div class="skills-grid" id="skills-grid">
                @foreach($portfolio->skills as $skill)
                <div class="skill-item" id="skill-item-{{ $skill->id }}">
                    <div class="skill-info">
                        <span class="skill-name">{{ $skill->name }}</span>
                        <div class="skill-bar-wrap">
                            <div class="skill-bar" style="width:{{ $skill->levelPercentage() }}%;background:{{ $skill->levelColor() }}"></div>
                        </div>
                        <span class="skill-level-badge skill-{{ $skill->level }}">{{ $skill->levelLabel() }}</span>
                    </div>
                    <form method="POST" action="{{ route('dashboard.skills.destroy', $skill->id) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon-danger" id="del-skill-{{ $skill->id }}" title="Remove skill">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add Skill Form --}}
            <form method="POST" action="{{ route('dashboard.skills.store') }}" class="skill-add-form" id="add-skill-form">
                @csrf
                <div class="skill-add-row">
                    <input type="text" name="name" placeholder="Skill name (e.g. Laravel, Figma...)"
                           id="skill-name-input" maxlength="80" required>
                    <select name="level" id="skill-level-select">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate" selected>Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                    <button type="submit" class="btn-primary btn-sm" id="add-skill-btn">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </form>
        </div>

        {{-- ═══ 3. CONTACT & NETWORKS ════════════════════════ --}}
        <div class="dashboard-card" id="section-contact">
            <div class="card-header">
                <h2><i class="fas fa-address-card"></i> Contact & Networks</h2>
                <span class="card-count">{{ $portfolio->socialLinks->count() }} channels</span>
            </div>

            <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
                Add, update, or remove contact channels. Only the channels you add here will appear on your public portfolio page.
            </p>

            {{-- Locked Email Field --}}
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Account Email (Primary Contact)</label>
                <div class="input-wrapper" style="opacity: 0.75;">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" value="{{ auth()->user()->email }}" disabled>
                </div>
                <small style="color:var(--text-muted); margin-top:.3rem; display:block">
                    Your login email. If you'd like to add another email, choose "Gmail" from the dropdown below.
                </small>
            </div>

            {{-- Existing Active Links --}}
            @if($portfolio->socialLinks->count())
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem;">
                <h3 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">Active Channels</h3>
                @foreach($portfolio->socialLinks as $link)
                <div class="project-item" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--surface-light); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <span style="font-size: 1.25rem; color: var(--primary); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(196,120,58,0.1); border-radius: 50%;">
                            <i class="{{ $link->iconClass() }}"></i>
                        </span>
                        <div>
                            <strong style="display: block; font-size: 0.88rem; color: var(--text);">{{ ucfirst($link->platform) }}</strong>
                            <span style="font-size: 0.8rem; color: var(--text-muted); word-break: break-all;">{{ $link->url }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.social.destroy', $link->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon-danger" title="Remove platform" style="padding: 0.5rem; border-radius: 6px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 2rem; border: 1px dashed var(--border); border-radius: var(--radius-md); color: var(--text-muted); margin-bottom: 2.5rem; font-size: 0.9rem;">
                <i class="fas fa-link" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                No custom channels added yet. Add one below to display on your public profile!
            </div>
            @endif

            {{-- Add Social Link Form --}}
            <div style="background: rgba(0,0,0,0.12); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border);">
                <h3 style="font-size: 0.95rem; font-family: var(--font-display); color: var(--accent); margin-bottom: 1.25rem;">+ Add Connection Channel</h3>
                <form method="POST" action="{{ route('dashboard.social.store') }}" class="dash-form">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="platform-select">Platform / Medium</label>
                            <select name="platform" id="platform-select" style="width: 100%; padding: 0.75rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-md); color: var(--text);" required>
                                <option value="">— Select Platform —</option>
                                <option value="website">Website</option>
                                <option value="github">GitHub</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                                <option value="snapchat">Snapchat</option>
                                <option value="twitter">Twitter / X</option>
                                <option value="youtube">YouTube</option>
                                <option value="behance">Behance</option>
                                <option value="gmail">Gmail</option>
                                <option value="phone">Phone</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="telegram">Telegram</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="platform-url">URL / Value</label>
                            <input type="text" name="url" id="platform-url" placeholder="https://... or username or phone number" style="width: 100%; padding: 0.75rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-md); color: var(--text);" required>
                        </div>
                    </div>
                    @error('platform')<span class="form-error" style="display:block; margin-bottom:0.5rem;">{{ $message }}</span>@enderror
                    @error('url')<span class="form-error" style="display:block; margin-bottom:0.5rem;">{{ $message }}</span>@enderror
                    <button type="submit" class="btn-primary" style="padding: 0.7rem 1.5rem; font-size: 0.88rem;">
                        <i class="fas fa-plus"></i> Add Channel
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══ 4. CV UPLOAD ═══════════════════════════════════ --}}
        <div class="dashboard-card" id="section-cv">
            <div class="card-header">
                <h2><i class="fas fa-file-pdf"></i> CV / Resume</h2>
            </div>

            @if($portfolio->cv_path)
            <div class="current-cv-info">
                <i class="fas fa-file-pdf cv-icon"></i>
                <div>
                    <strong>CV uploaded ✓</strong>
                    <small style="display:block;opacity:.7">Stored securely. Only visible to viewers of your portfolio.</small>
                </div>
                <a href="{{ route('portfolio.cv.download', $portfolio->slug) }}"
                   target="_blank" class="btn-ghost-sm" id="preview-cv-btn">
                    <i class="fas fa-eye"></i> Preview
                </a>
            </div>
            @endif

            <form method="POST" action="{{ route('dashboard.portfolio.cv') }}"
                  enctype="multipart/form-data" class="dash-form" id="cv-form">
                @csrf
                <label for="cv" class="file-drop-zone" id="cv-drop-zone">
                    <i class="fas fa-file-upload"></i>
                    <span>{{ $portfolio->cv_path ? 'Replace CV' : 'Upload PDF CV' }}</span>
                    <small>PDF only · max 5MB</small>
                    <input type="file" name="cv" id="cv" accept=".pdf"
                           style="display:none" onchange="showCvName(this)">
                </label>
                <span id="cv-filename" class="filename-display"></span>
                @error('cv')<span class="form-error">{{ $message }}</span>@enderror
                <button type="submit" class="btn-secondary" id="upload-cv-btn" style="margin-top:1rem">
                    <i class="fas fa-upload"></i>
                    {{ $portfolio->cv_path ? 'Replace CV' : 'Upload CV' }}
                </button>
            </form>
        </div>

        {{-- ═══ 5. PROJECTS ════════════════════════════════════ --}}
        <div class="dashboard-card" id="section-projects">
            <div class="card-header">
                <h2><i class="fas fa-code"></i> Projects</h2>
                <span class="card-count">{{ $portfolio->projects->count() }} project{{ $portfolio->projects->count() !== 1 ? 's' : '' }}</span>
            </div>

            {{-- Mini project list preview (first 3) --}}
            @if($portfolio->projects->count())
            <div class="projects-list" id="projects-list">
                @foreach($portfolio->projects->take(3) as $project)
                <div class="project-list-item" id="project-item-{{ $project->id }}">
                    <div class="project-list-header">
                        @if($project->coverUrl())
                            <img src="{{ $project->coverUrl() }}" alt="{{ $project->title }}" class="project-thumb">
                        @elseif($project->images->count())
                            @php $firstImg = $project->images->firstWhere('file_type', 'image') ?? $project->images->first(); @endphp
                            @if($firstImg && $firstImg->isImage())
                                <img src="{{ asset('storage/' . $firstImg->image_path) }}" alt="" class="project-thumb">
                            @else
                                <div class="project-thumb-placeholder" style="color:var(--danger)"><i class="fas fa-file-pdf"></i></div>
                            @endif
                        @else
                            <div class="project-thumb-placeholder"><i class="fas fa-code"></i></div>
                        @endif
                        <div class="project-list-info">
                            <h4>{{ $project->title }}</h4>
                            <p>{{ Str::limit($project->description, 80) }}</p>
                            @if($project->tech_stack)
                            <div class="tech-tags-sm">
                                @foreach(array_slice(array_map('trim', explode(',', $project->tech_stack)), 0, 4) as $tech)
                                    <span class="tech-tag-sm">{{ $tech }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <a href="{{ route('dashboard.projects.edit', $project->id) }}"
                           class="btn-ghost-sm" id="edit-proj-dash-{{ $project->id }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                @endforeach
                @if($portfolio->projects->count() > 3)
                    <p style="text-align:center;font-size:.82rem;color:var(--text-muted);margin:.5rem 0 0">
                        +{{ $portfolio->projects->count() - 3 }} more &mdash;
                        <a href="{{ route('dashboard.projects.index') }}" style="color:var(--primary)">view all</a>
                    </p>
                @endif
            </div>
            @endif

            {{-- Hub CTA --}}
            <div class="projects-hub-cta">
                <a href="{{ route('dashboard.projects.index') }}" class="btn-secondary" id="manage-projects-btn">
                    <i class="fas fa-th-large"></i> Manage All Projects
                </a>
                <a href="{{ route('dashboard.projects.create') }}" class="btn-primary" id="add-project-dash-btn">
                    <i class="fas fa-plus"></i> Add Project
                </a>
            </div>
        </div>

        {{-- ═══ 6. PASSWORD CHANGE ═══════════════════════════ --}}
        <div class="dashboard-card" id="section-password">
            <div class="card-header">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
            </div>
            <form method="POST" action="{{ route('password.change.update') }}" class="dash-form" id="password-form">
                @csrf
                @if(!auth()->user()->must_change_password)
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="current_password" id="current_password"
                               placeholder="Your current password" autocomplete="current-password">
                    </div>
                    @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                @endif
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="new_password"
                               placeholder="Min. 8 chars, uppercase, numbers" autocomplete="new-password">
                    </div>
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Repeat new password" autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="change-password-btn">
                    <i class="fas fa-shield-alt"></i> Update Password
                </button>
            </form>
        </div>

    </main>
</div>
@endsection

@section('styles')
<style>
/* ── Projects Hub CTA ── */
.projects-hub-cta {
    display: flex;
    gap: .75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.projects-hub-cta a { flex: 1; text-align: center; }

/* ── Dashboard Extra Styles ── */
.dash-picture-row {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    margin-bottom: 1.75rem;
    padding-bottom: 1.75rem;
    border-bottom: 1px solid var(--border);
}
.dash-avatar-img {
    width: 100px; height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
}
.dash-avatar-placeholder {
    width: 100px; height: 100px;
    border-radius: 50%;
    background: rgba(196,120,58,0.15);
    color: var(--primary);
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid var(--border);
}
.dash-picture-upload { flex: 1; }
.file-drop-compact { padding: 1rem; }
.file-drop-compact i { font-size: 1.5rem; margin-bottom: .4rem; }

/* Skills */
.card-count { font-size: .8rem; color: var(--text-muted); }
.skills-grid { display: flex; flex-direction: column; gap: .6rem; margin-bottom: 1.25rem; }
.skill-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .75rem 1rem;
    background: rgba(0,0,0,.1);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
}
body.light-mode .skill-item { background: rgba(196,120,58,.04); }
.skill-info { flex: 1; display: flex; align-items: center; gap: 1rem; }
.skill-name { font-weight: 600; font-size: .9rem; min-width: 120px; }
.skill-bar-wrap {
    flex: 1;
    height: 6px;
    background: var(--border);
    border-radius: 99px;
    overflow: hidden;
}
.skill-bar { height: 100%; border-radius: 99px; transition: width .5s ease; }
.skill-level-badge {
    font-size: .7rem;
    font-weight: 600;
    padding: .15rem .55rem;
    border-radius: var(--radius-full);
    background: rgba(196,120,58,.1);
    color: var(--primary);
    white-space: nowrap;
}
.skill-add-form { margin-top: 1rem; }
.skill-add-row {
    display: flex;
    gap: .75rem;
    align-items: center;
}
.skill-add-row input { flex: 1; }
.skill-add-row select { width: 150px; flex-shrink: 0; }
.btn-icon-danger {
    background: transparent;
    border: none;
    color: var(--danger);
    cursor: pointer;
    opacity: .6;
    transition: opacity var(--t-fast);
    font-size: .9rem;
    padding: .4rem;
}
.btn-icon-danger:hover { opacity: 1; }
.char-count { font-size: .78rem; color: var(--text-muted); display: block; margin-top: .3rem; }

@media (max-width: 600px) {
    .skill-add-row { flex-direction: column; }
    .skill-add-row select { width: 100%; }
    .dash-picture-row { flex-direction: column; }
}
</style>
@endsection

@section('scripts')
<script>
function toggleSection(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img   = document.getElementById('avatar-preview-img');
            const ph    = document.getElementById('avatar-preview-placeholder');
            if (img) { img.src = e.target.result; }
            else if (ph) {
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.id  = 'avatar-preview-img';
                newImg.className = 'dash-avatar-img';
                ph.replaceWith(newImg);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showCvName(input) {
    const display = document.getElementById('cv-filename');
    if (display) display.textContent = input.files[0] ? '📄 ' + input.files[0].name : '';
}

function showFileCount(input) {
    const display = document.getElementById('project-files-count');
    if (display && input.files.length > 0) {
        const names = Array.from(input.files).map(f => f.name).join(', ');
        display.textContent = `📎 ${input.files.length} file(s): ${names}`;
    }
}

// Bio character counter
const bioInput = document.getElementById('bio');
const bioCount = document.getElementById('bio-count');
if (bioInput && bioCount) {
    bioInput.addEventListener('input', () => {
        bioCount.textContent = bioInput.value.length + ' / 2000';
    });
}

// Smooth scroll for sidebar links
document.querySelectorAll('.dash-nav-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Highlight active sidebar link on scroll
const sections = document.querySelectorAll('.dashboard-card[id]');
const navLinks = document.querySelectorAll('.dash-nav-link');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            navLinks.forEach(l => l.classList.remove('active'));
            const active = document.querySelector(`.dash-nav-link[href="#${entry.target.id}"]`);
            if (active) active.classList.add('active');
        }
    });
}, { threshold: 0.3 });
sections.forEach(s => observer.observe(s));
</script>
@endsection
