@extends('layouts.app')
@section('title', 'Edit Project — ' . $project->title)

@section('content')
<div class="dashboard-layout">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                @if($portfolio->profile_picture_path)
                    <img src="{{ asset('storage/' . $portfolio->profile_picture_path) }}" alt="Profile">
                @else
                    <div class="avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
            </div>
            <h3>{{ auth()->user()->name }}</h3>
            <p class="sidebar-headline">{{ $portfolio->headline ?? 'Graduate' }}</p>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a>
            <a href="{{ route('dashboard.projects.index') }}" class="sidebar-link active"><i class="fas fa-code"></i> Projects</a>
            <a href="{{ route('dashboard.projects.create') }}" class="sidebar-link"><i class="fas fa-plus-circle"></i> Add Project</a>
        </nav>

        {{-- Delete Project --}}
        <form method="POST" action="{{ route('dashboard.projects.destroy', $project->id) }}"
              style="margin-top:auto;padding-top:1rem">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger-full" id="delete-project-btn"
                    onclick="return confirm('Delete this project permanently? All images will be removed.')">
                <i class="fas fa-trash"></i> Delete This Project
            </button>
        </form>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────────── --}}
    <main class="dashboard-main">

        {{-- Breadcrumb --}}
        <div class="proj-breadcrumb">
            <a href="{{ route('dashboard.projects.index') }}"><i class="fas fa-code"></i> Projects</a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ Str::limit($project->title, 40) }}</span>
        </div>

        <form method="POST" action="{{ route('dashboard.projects.update', $project->id) }}"
              enctype="multipart/form-data" id="edit-project-form" novalidate>
            @csrf @method('PUT')

            <div class="proj-form-grid">

                {{-- ── Left Column ─────────────────────────────────── --}}
                <div class="proj-form-main">

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-info-circle"></i> Project Details</h2>
                        </div>

                        <div class="form-group">
                            <label for="title">Project Title <span class="required">*</span></label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title', $project->title) }}"
                                   required maxlength="255">
                            @error('title')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="required">*</span></label>
                            <textarea name="description" id="description" rows="6"
                                      required maxlength="5000">{{ old('description', $project->description) }}</textarea>
                            <small class="char-count" id="desc-count">{{ strlen(old('description', $project->description ?? '')) }} / 5000</small>
                            @error('description')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="tech_stack">Technologies Used</label>
                            <input type="text" name="tech_stack" id="tech_stack"
                                   value="{{ old('tech_stack', $project->tech_stack) }}"
                                   placeholder="Laravel, Vue.js, MySQL (comma-separated)">
                            <div id="tech-preview" class="phc-tags" style="margin-top:.5rem;min-height:1.5rem">
                                @foreach($project->techStackArray() as $t)
                                    <span class="tech-badge">{{ $t }}</span>
                                @endforeach
                            </div>
                            @error('tech_stack')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="project_url">Live URL</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-external-link-alt input-icon"></i>
                                    <input type="url" name="project_url" id="project_url"
                                           value="{{ old('project_url', $project->project_url) }}"
                                           placeholder="https://yourproject.com">
                                </div>
                                @error('project_url')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="github_url">GitHub URL</label>
                                <div class="input-wrapper">
                                    <i class="fab fa-github input-icon"></i>
                                    <input type="url" name="github_url" id="github_url"
                                           value="{{ old('github_url', $project->github_url) }}"
                                           placeholder="https://github.com/user/repo">
                                </div>
                                @error('github_url')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="proj-form-footer" style="border-top:1px solid var(--border);padding-top:1.25rem;margin-top:1rem">
                            <a href="{{ route('dashboard.projects.index') }}" class="btn-ghost" id="cancel-edit-btn">Cancel</a>
                            <button type="submit" class="btn-primary" id="save-project-btn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ── Right Column ─────────────────────────────────── --}}
                <div class="proj-form-side">

                    {{-- Cover Image --}}
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-image"></i> Cover Image</h2>
                        </div>

                        @if($project->coverUrl())
                        <div class="current-cover-wrap" id="current-cover-wrap">
                            <img src="{{ $project->coverUrl() }}" alt="Current cover"
                                 class="current-cover-img" id="current-cover-img">
                            <div class="current-cover-actions">
                                <span class="current-label">Current cover</span>
                                @if($project->cover_image_path)
                                <form method="POST" action="{{ route('dashboard.projects.cover.remove', $project->id) }}" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost-sm btn-danger-text" id="remove-cover-btn"
                                            onclick="return confirm('Remove cover image?')">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div id="new-cover-preview-wrap" style="display:none;margin-bottom:1rem">
                            <img id="new-cover-preview-img" src="" alt="New cover preview"
                                 style="width:100%;height:180px;object-fit:cover;border-radius:var(--radius-md);border:2px dashed var(--primary)">
                            <small style="color:var(--primary);margin-top:.3rem;display:block">
                                <i class="fas fa-info-circle"></i> New cover — will replace current after saving
                            </small>
                        </div>

                        <label for="cover_image" class="file-drop-zone" id="cover-drop-zone" style="padding:1.25rem">
                            <i class="fas fa-exchange-alt" style="font-size:1.8rem;color:var(--primary)"></i>
                            <span>{{ $project->coverUrl() ? 'Replace cover image' : 'Upload cover image' }}</span>
                            <small>JPG, PNG, WebP · max 8MB · optimized to 1200×800</small>
                            <input type="file" name="cover_image" id="cover_image"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   style="display:none" onchange="previewNewCover(this)">
                        </label>
                        @error('cover_image')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Gallery Manager --}}
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-images"></i> Gallery</h2>
                            <span class="card-count">{{ $project->images->count() }} file(s)</span>
                        </div>

                        {{-- Existing Images --}}
                        @if($project->images->count() > 0)
                        <div class="edit-gallery-grid" id="existing-gallery">
                            @foreach($project->images as $img)
                            <div class="edit-gallery-item" id="gallery-item-{{ $img->id }}">
                                @if($img->isImage())
                                    <img src="{{ $img->thumbnailUrl() }}" alt="{{ $img->alt_text ?? $project->title }}" loading="lazy">
                                    {{-- Set as cover overlay --}}
                                    <div class="gallery-item-overlay">
                                        <form method="POST" action="{{ route('dashboard.projects.images.set-cover', $img->id) }}">
                                            @csrf
                                            <button type="submit" class="gallery-set-cover-btn" id="set-cover-{{ $img->id }}"
                                                    title="Set as cover">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @if($project->cover_image_path === $img->image_path)
                                        <span class="gallery-cover-badge"><i class="fas fa-star"></i> Cover</span>
                                    @endif
                                @else
                                    <a href="{{ $img->url() }}" target="_blank" class="gallery-pdf-item">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>PDF</span>
                                    </a>
                                @endif
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('dashboard.projects.images.destroy', $img->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="gallery-delete-btn" id="del-img-{{ $img->id }}"
                                            title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                            @endforeach

                            {{-- Add More Files Button --}}
                            <form method="POST" action="{{ route('dashboard.projects.images.add', $project->id) }}"
                                  enctype="multipart/form-data" id="add-more-form">
                                @csrf
                                <label class="edit-gallery-add" title="Add more files" id="add-more-btn">
                                    <i class="fas fa-plus"></i>
                                    <input type="file" name="images[]" multiple
                                           accept="image/*,.pdf,application/pdf"
                                           style="display:none"
                                           onchange="this.form.submit()">
                                </label>
                            </form>
                        </div>
                        @endif

                        {{-- Upload New Files --}}
                        @if($project->images->count() === 0)
                        <label for="gallery-input" class="file-drop-zone" id="gallery-drop-zone">
                            <i class="fas fa-images" style="font-size:2rem;color:var(--primary)"></i>
                            <span>Add gallery files</span>
                            <small>Images or PDFs · max 8MB each</small>
                            <input type="file" name="gallery[]" id="gallery-input" multiple
                                   accept="image/*,.pdf,application/pdf" style="display:none"
                                   onchange="previewNewGallery(this)">
                        </label>
                        <div id="new-gallery-preview" class="gallery-preview-grid" style="margin-top:1rem"></div>
                        @else
                        {{-- New gallery files as hidden form --}}
                        <div style="margin-top:1rem">
                            <label for="gallery-input" class="file-drop-zone" style="padding:1rem" id="gallery-drop-zone">
                                <i class="fas fa-plus" style="color:var(--primary)"></i>
                                <span>Add more files to gallery</span>
                                <small>Images or PDFs · max 8MB each</small>
                                <input type="file" name="gallery[]" id="gallery-input" multiple
                                       accept="image/*,.pdf,application/pdf" style="display:none"
                                       onchange="previewNewGallery(this)">
                            </label>
                            <div id="new-gallery-preview" class="gallery-preview-grid" style="margin-top:.75rem"></div>
                        </div>
                        @endif

                        @error('gallery')<span class="form-error">{{ $message }}</span>@enderror
                        @error('gallery.*')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                </div>
            </div>

        </form>
    </main>
</div>
@endsection

@section('styles')
<style>
.proj-breadcrumb {
    display: flex; align-items: center; gap: .6rem;
    font-size: .85rem; color: var(--text-muted); margin-bottom: 1.5rem;
}
.proj-breadcrumb a { color: var(--primary); text-decoration: none; }
.proj-breadcrumb i.fa-chevron-right { font-size: .65rem; opacity: .5; }

.proj-form-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
    align-items: start;
}
.proj-form-main, .proj-form-side { display: flex; flex-direction: column; gap: 1.5rem; }

.proj-form-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 1rem;
}

/* Cover */
.current-cover-wrap { margin-bottom: 1rem; }
.current-cover-img {
    width: 100%; height: 180px; object-fit: cover;
    border-radius: var(--radius-md); border: 1px solid var(--border);
}
.current-cover-actions {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: .5rem;
}
.current-label { font-size: .8rem; color: var(--text-muted); }
.btn-danger-text { color: var(--danger) !important; }

/* Gallery Grid - Edit */
.edit-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: .5rem;
}
.edit-gallery-item {
    position: relative;
    border-radius: var(--radius-sm);
    overflow: hidden;
    aspect-ratio: 1;
    background: rgba(0,0,0,.1);
    border: 1px solid var(--border);
}
.edit-gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.gallery-item-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--t-fast);
}
.edit-gallery-item:hover .gallery-item-overlay { opacity: 1; }
.gallery-set-cover-btn {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-full);
    padding: .3rem .6rem;
    font-size: .7rem;
    cursor: pointer;
    transition: background var(--t-fast);
}
.gallery-set-cover-btn:hover { background: #a8622a; }
.gallery-cover-badge {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: var(--primary);
    color: #fff;
    font-size: .6rem;
    text-align: center;
    padding: .15rem;
    font-weight: 600;
}
.gallery-pdf-item {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: var(--danger);
    text-decoration: none;
    font-size: .6rem; gap: .25rem;
}
.gallery-pdf-item i { font-size: 1.6rem; }
.gallery-delete-btn {
    position: absolute;
    top: 2px; right: 2px;
    background: rgba(0,0,0,.7);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 18px; height: 18px;
    cursor: pointer;
    font-size: .55rem;
    display: flex; align-items: center; justify-content: center;
    transition: background var(--t-fast);
    z-index: 10;
}
.gallery-delete-btn:hover { background: var(--danger); }
.edit-gallery-add {
    aspect-ratio: 1;
    border: 2px dashed var(--border);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 1.3rem;
    transition: border-color var(--t-fast), color var(--t-fast);
}
.edit-gallery-add:hover { border-color: var(--primary); color: var(--primary); }

/* New gallery preview */
.gallery-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: .5rem;
}
.gallery-preview-item {
    position: relative;
    border-radius: var(--radius-sm);
    overflow: hidden;
    aspect-ratio: 1;
    background: rgba(0,0,0,.1);
    border: 2px dashed rgba(196,120,58,.3);
}
.gallery-preview-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
.gallery-preview-pdf {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: .2rem; color: var(--danger); font-size: .6rem;
}
.gallery-preview-pdf i { font-size: 1.4rem; }
.gallery-preview-remove {
    position: absolute; top: 2px; right: 2px;
    background: rgba(0,0,0,.7); color: #fff;
    border: none; border-radius: 50%;
    width: 18px; height: 18px;
    cursor: pointer; font-size: .55rem;
    display: flex; align-items: center; justify-content: center;
}
.gallery-preview-remove:hover { background: var(--danger); }

/* Delete button */
.btn-danger-full {
    width: 100%;
    background: rgba(248,113,113,.1);
    color: var(--danger);
    border: 1px solid rgba(248,113,113,.25);
    border-radius: var(--radius-md);
    padding: .75rem 1rem;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--t-fast);
    display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-danger-full:hover { background: rgba(248,113,113,.2); }

.required { color: var(--danger); }

@media (max-width: 900px) {
    .proj-form-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('scripts')
<script>
// ── New cover preview ─────────────────────────────────────────
function previewNewCover(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('new-cover-preview-img').src = e.target.result;
        document.getElementById('new-cover-preview-wrap').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// ── New gallery files preview ─────────────────────────────────
let newGalleryFiles = new DataTransfer();

function previewNewGallery(input) {
    for (const file of input.files) newGalleryFiles.items.add(file);
    renderNewGallery();
    input.files = newGalleryFiles.files;
}

function removeNewGalleryFile(index) {
    const dt = new DataTransfer();
    const files = newGalleryFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) dt.items.add(files[i]);
    }
    newGalleryFiles = dt;
    document.getElementById('gallery-input').files = newGalleryFiles.files;
    renderNewGallery();
}

function renderNewGallery() {
    const grid = document.getElementById('new-gallery-preview');
    if (!grid) return;
    grid.innerHTML = '';
    const files = newGalleryFiles.files;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const item = document.createElement('div');
        item.className = 'gallery-preview-item';
        if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
            item.innerHTML = `<div class="gallery-preview-pdf"><i class="fas fa-file-pdf"></i><span>${file.name.substring(0,8)}</span></div>`;
        } else {
            const img = document.createElement('img');
            const r = new FileReader();
            r.onload = e => img.src = e.target.result;
            r.readAsDataURL(file);
            item.appendChild(img);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'gallery-preview-remove';
        btn.innerHTML = '<i class="fas fa-times"></i>';
        btn.onclick = () => removeNewGalleryFile(i);
        item.appendChild(btn);
        grid.appendChild(item);
    }
}

// ── Tech stack preview ────────────────────────────────────────
document.getElementById('tech_stack')?.addEventListener('input', function() {
    const preview = document.getElementById('tech-preview');
    const tags = this.value.split(',').map(t => t.trim()).filter(Boolean);
    preview.innerHTML = tags.map(t => `<span class="tech-badge">${t}</span>`).join('');
});

// ── Description counter ───────────────────────────────────────
const desc = document.getElementById('description');
const cnt  = document.getElementById('desc-count');
desc?.addEventListener('input', () => cnt.textContent = desc.value.length + ' / 5000');
</script>
@endsection
