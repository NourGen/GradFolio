@extends('layouts.app')
@section('title', 'Add New Project')

@section('content')
<div class="dashboard-layout">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
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
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a>
            <a href="{{ route('dashboard.projects.index') }}" class="sidebar-link"><i class="fas fa-code"></i> Projects</a>
            <a href="{{ route('dashboard.projects.create') }}" class="sidebar-link active"><i class="fas fa-plus-circle"></i> Add Project</a>
        </nav>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────────── --}}
    <main class="dashboard-main">

        {{-- Breadcrumb --}}
        <div class="proj-breadcrumb">
            <a href="{{ route('dashboard.projects.index') }}"><i class="fas fa-code"></i> Projects</a>
            <i class="fas fa-chevron-right"></i>
            <span>Add New</span>
        </div>

        <form method="POST" action="{{ route('dashboard.projects.store') }}"
              enctype="multipart/form-data" id="create-project-form" novalidate>
            @csrf

            <div class="proj-form-grid">

                {{-- ── Left Column: Details ────────────────────────── --}}
                <div class="proj-form-main">

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-info-circle"></i> Project Details</h2>
                        </div>

                        <div class="form-group">
                            <label for="title">Project Title <span class="required">*</span></label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title') }}"
                                   placeholder="e.g. E-Commerce Platform"
                                   required maxlength="255">
                            @error('title')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="required">*</span></label>
                            <textarea name="description" id="description" rows="6"
                                      placeholder="What did you build? What problem does it solve? What was your role?"
                                      required maxlength="5000">{{ old('description') }}</textarea>
                            <small class="char-count" id="desc-count">{{ strlen(old('description', '')) }} / 5000</small>
                            @error('description')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="tech_stack">Technologies Used</label>
                            <input type="text" name="tech_stack" id="tech_stack"
                                   value="{{ old('tech_stack') }}"
                                   placeholder="Laravel, Vue.js, MySQL, Tailwind CSS (comma-separated)">
                            <small style="color:var(--text-muted)">Separate each technology with a comma</small>
                            @error('tech_stack')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        {{-- Tech preview --}}
                        <div id="tech-preview" class="phc-tags" style="margin-top:.5rem;min-height:1.5rem"></div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="project_url">Live URL</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-external-link-alt input-icon"></i>
                                    <input type="url" name="project_url" id="project_url"
                                           value="{{ old('project_url') }}"
                                           placeholder="https://yourproject.com">
                                </div>
                                @error('project_url')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="github_url">GitHub URL</label>
                                <div class="input-wrapper">
                                    <i class="fab fa-github input-icon"></i>
                                    <input type="url" name="github_url" id="github_url"
                                           value="{{ old('github_url') }}"
                                           placeholder="https://github.com/user/repo">
                                </div>
                                @error('github_url')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Right Column: Images ─────────────────────────── --}}
                <div class="proj-form-side">

                    {{-- Cover Image --}}
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-image"></i> Cover Image</h2>
                        </div>
                        <p class="proj-section-hint">The main image shown in the directory and at the top of your project.</p>

                        <div id="cover-preview-wrap" style="display:none;margin-bottom:1rem">
                            <img id="cover-preview-img" src="" alt="Cover preview"
                                 style="width:100%;height:200px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border)">
                            <button type="button" class="btn-ghost-sm" style="margin-top:.5rem" onclick="clearCover()" id="clear-cover-btn">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>

                        <label for="cover_image" class="file-drop-zone" id="cover-drop-zone">
                            <i class="fas fa-cloud-upload-alt" style="font-size:2.5rem;color:var(--primary)"></i>
                            <span>Click or drag a cover image</span>
                            <small>JPG, PNG, WebP · max 8MB · auto-optimized to 1200×800</small>
                            <input type="file" name="cover_image" id="cover_image"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   style="display:none" onchange="previewCover(this)">
                        </label>
                        @error('cover_image')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Gallery Images --}}
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-images"></i> Gallery</h2>
                        </div>
                        <p class="proj-section-hint">Add screenshots, mockups, or PDF documents. Up to 20 files.</p>

                        <label for="gallery-input" class="file-drop-zone" id="gallery-drop-zone">
                            <i class="fas fa-images" style="font-size:2.5rem;color:var(--primary)"></i>
                            <span>Click or drag gallery files</span>
                            <small>JPG, PNG, WebP, GIF, PDF · max 8MB each</small>
                            <input type="file" name="gallery[]" id="gallery-input"
                                   multiple accept="image/*,.pdf,application/pdf"
                                   style="display:none" onchange="previewGallery(this)">
                        </label>
                        @error('gallery')<span class="form-error">{{ $message }}</span>@enderror
                        @error('gallery.*')<span class="form-error">{{ $message }}</span>@enderror

                        {{-- Preview Grid --}}
                        <div id="gallery-preview" class="gallery-preview-grid" style="margin-top:1rem"></div>
                    </div>

                </div>
            </div>

            {{-- Submit --}}
            <div class="proj-form-footer">
                <a href="{{ route('dashboard.projects.index') }}" class="btn-ghost" id="cancel-create-btn">Cancel</a>
                <button type="submit" class="btn-primary" id="submit-project-btn">
                    <i class="fas fa-rocket"></i> Add Project
                </button>
            </div>

        </form>
    </main>
</div>
@endsection

@section('styles')
<style>
.proj-breadcrumb {
    display: flex;
    align-items: center;
    gap: .6rem;
    font-size: .85rem;
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}
.proj-breadcrumb a { color: var(--primary); text-decoration: none; }
.proj-breadcrumb a:hover { text-decoration: underline; }
.proj-breadcrumb i.fa-chevron-right { font-size: .65rem; opacity: .5; }

.proj-form-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
    align-items: start;
}
.proj-form-main { display: flex; flex-direction: column; gap: 1.5rem; }
.proj-form-side { display: flex; flex-direction: column; gap: 1.5rem; }

.proj-section-hint {
    font-size: .83rem;
    color: var(--text-muted);
    margin: -.5rem 0 1rem;
    line-height: 1.5;
}

.proj-form-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

/* Gallery preview */
.gallery-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: .5rem;
}
.gallery-preview-item {
    position: relative;
    border-radius: var(--radius-sm);
    overflow: hidden;
    aspect-ratio: 1;
    background: rgba(0,0,0,.15);
}
.gallery-preview-item img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.gallery-preview-item .gallery-preview-pdf {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: .25rem;
    color: var(--danger);
    font-size: .65rem;
}
.gallery-preview-item .gallery-preview-pdf i { font-size: 1.6rem; }
.gallery-preview-remove {
    position: absolute;
    top: 3px; right: 3px;
    background: rgba(0,0,0,.7);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 20px; height: 20px;
    cursor: pointer;
    font-size: .6rem;
    display: flex; align-items: center; justify-content: center;
    transition: background var(--t-fast);
}
.gallery-preview-remove:hover { background: var(--danger); }

.required { color: var(--danger); }

@media (max-width: 900px) {
    .proj-form-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('scripts')
<script>
// ── Cover image preview ──────────────────────────────────────
let coverFileInput = document.getElementById('cover_image');
function previewCover(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cover-preview-img').src = e.target.result;
        document.getElementById('cover-preview-wrap').style.display = 'block';
        document.getElementById('cover-drop-zone').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
function clearCover() {
    coverFileInput.value = '';
    document.getElementById('cover-preview-wrap').style.display = 'none';
    document.getElementById('cover-drop-zone').style.display = '';
}

// ── Gallery preview with drag-and-drop ──────────────────────
let galleryFiles = new DataTransfer();

function previewGallery(input) {
    for (const file of input.files) {
        galleryFiles.items.add(file);
    }
    renderGalleryPreviews();
    // Sync DataTransfer back to input
    input.files = galleryFiles.files;
}

function removeGalleryFile(index) {
    const newDT = new DataTransfer();
    const files  = galleryFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) newDT.items.add(files[i]);
    }
    galleryFiles = newDT;
    document.getElementById('gallery-input').files = galleryFiles.files;
    renderGalleryPreviews();
}

function renderGalleryPreviews() {
    const grid = document.getElementById('gallery-preview');
    grid.innerHTML = '';
    const files = galleryFiles.files;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const item = document.createElement('div');
        item.className = 'gallery-preview-item';

        if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
            item.innerHTML = `<div class="gallery-preview-pdf"><i class="fas fa-file-pdf"></i><span>${file.name.substring(0,10)}</span></div>`;
        } else {
            const img = document.createElement('img');
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
            item.appendChild(img);
        }

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'gallery-preview-remove';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeGalleryFile(i);
        item.appendChild(removeBtn);
        grid.appendChild(item);
    }
}

// ── Tech stack live preview ──────────────────────────────────
document.getElementById('tech_stack')?.addEventListener('input', function() {
    const preview = document.getElementById('tech-preview');
    const tags = this.value.split(',').map(t => t.trim()).filter(Boolean);
    preview.innerHTML = tags.map(t =>
        `<span class="tech-badge">${t}</span>`
    ).join('');
});

// ── Description character counter ───────────────────────────
const descInput = document.getElementById('description');
const descCount = document.getElementById('desc-count');
descInput?.addEventListener('input', () => {
    descCount.textContent = descInput.value.length + ' / 5000';
});

// ── Drag and drop on file zones ──────────────────────────────
['cover-drop-zone', 'gallery-drop-zone'].forEach(id => {
    const zone = document.getElementById(id);
    if (!zone) return;
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        const input = zone.querySelector('input[type="file"]');
        if (input) {
            if (id === 'cover-drop-zone') {
                input.files = e.dataTransfer.files;
                previewCover(input);
            } else {
                for (const file of e.dataTransfer.files) galleryFiles.items.add(file);
                input.files = galleryFiles.files;
                renderGalleryPreviews();
            }
        }
    });
});
</script>
@endsection
