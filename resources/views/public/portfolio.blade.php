<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>{{ $portfolio->user->name }} — Portfolio</title>
    <meta name="description" content="{{ Str::limit(strip_tags($portfolio->bio ?? $portfolio->headline), 155) }}" />
    <meta name="author" content="{{ $portfolio->user->name }}">
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Original BSA Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/bsa-portfolio/index.css') }}" />

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $portfolio->user->name }} — Portfolio">
    <meta property="og:description" content="{{ Str::limit(strip_tags($portfolio->bio ?? $portfolio->headline), 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($portfolio->profilePictureUrl())
        <meta property="og:image" content="{{ $portfolio->profilePictureUrl() }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $portfolio->user->name }} — Portfolio">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($portfolio->bio ?? $portfolio->headline), 155) }}">
    @if($portfolio->profilePictureUrl())
        <meta name="twitter:image" content="{{ $portfolio->profilePictureUrl() }}">
    @endif

    <style>
        /* Interactive 3D Canvas Container */
        .hero-3d-canvas-wrap {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        #hero-3d-canvas {
            width: 100%;
            height: 100%;
            display: block;
            opacity: 0.65;
            transition: opacity 0.5s ease;
        }
        body.light-mode #hero-3d-canvas {
            opacity: 0.45;
        }

        /* Lightbox Gallery Modal custom styles (matching colors of BSA portfolio) */
        .gallery-modal-v3 {
            position: fixed;
            inset: 0;
            background: rgba(26, 14, 8, 0.95);
            backdrop-filter: blur(8px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            padding: 1.5rem;
        }
        body.light-mode .gallery-modal-v3 {
            background: rgba(250, 247, 242, 0.96);
        }
        .gallery-modal-v3.active {
            display: flex;
            opacity: 1;
        }
        .gallery-modal-container {
            position: relative;
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .gallery-close-btn {
            position: absolute;
            top: -3.5rem;
            right: 0;
            background: transparent;
            border: none;
            color: var(--color-text);
            font-size: 2.5rem;
            cursor: pointer;
            opacity: 0.75;
            transition: opacity 0.2s;
        }
        .gallery-close-btn:hover {
            opacity: 1;
        }
        .gallery-active-image-wrap {
            position: relative;
            width: 100%;
            height: 60vh;
            max-height: 550px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gallery-active-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }
        .gallery-nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border-radius: var(--radius-pill);
            background: rgba(44, 26, 14, 0.7);
            color: var(--color-text);
            border: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.25rem;
            transition: all 0.2s ease;
            user-select: none;
            z-index: 10;
        }
        body.light-mode .gallery-nav-arrow {
            background: rgba(237, 227, 214, 0.85);
            color: #1A0E08;
        }
        .gallery-nav-arrow:hover {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: #1A0E08;
        }
        body.light-mode .gallery-nav-arrow:hover {
            color: #fff;
        }
        .arrow-prev { left: 15px; }
        .arrow-next { right: 15px; }
        
        .gallery-modal-caption {
            margin-top: 1rem;
            color: var(--color-text);
            font-size: 0.95rem;
            text-align: center;
            width: 100%;
            min-height: 1.5rem;
        }
        .gallery-modal-counter {
            color: var(--color-text-muted);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
    </style>
  </head>
  <body>
    
    <!-- ── HEADER NAVBAR ── -->
    <header class="header" id="top">
      <div class="container nav-wrap">
        <a class="logo" href="#top">
          <img src="{{ asset('Matrials/BSA-icon-with-out-bg.png') }}" alt="BSA" class="logo-icon" />
          <span class="trainee-Name">{{ $portfolio->user->name }}</span>
        </a>
        <div class="nav-controls">
          <button class="theme-toggle" id="theme-toggle" aria-label="Toggle light/dark mode">
            <span class="theme-icon">☀️</span>
          </button>
          <button class="menu-btn" aria-label="Toggle menu" aria-expanded="false">
            ☰
          </button>
        </div>
        <nav class="nav">
          <ul class="nav-list">
            <li><a href="#home">Home</a></li>
            @if($portfolio->bio) <li><a href="#about">About</a></li> @endif
            @if($portfolio->skills->count()) <li><a href="#skills">Skills</a></li> @endif
            @if($portfolio->projects->count()) <li><a href="#projects">Projects</a></li> @endif
            <li><a href="#contact">Contact</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main>
      
      <!-- ── HERO SECTION ── -->
      <section class="hero section" id="home">
        <!-- 3D Canvas and Glow Overlays -->
        <div class="hero-3d-canvas-wrap">
            <canvas id="hero-3d-canvas"></canvas>
        </div>
        <div class="hero-glow glow-1" aria-hidden="true"></div>
        <div class="hero-glow glow-2" aria-hidden="true"></div>
        
        <div class="container hero-grid">
          <div class="hero-content">
            <p class="eyebrow">Hello, I'm</p>
            <div class="hero-name-wrap" style="display: flex; align-items: center; gap: clamp(1rem, 2vw, 1.5rem); margin-bottom: 1rem; flex-wrap: wrap;">
              @if($portfolio->profilePictureUrl())
                <img src="{{ $portfolio->profilePictureUrl() }}" 
                     class="avatar-icon" 
                     style="width: clamp(65px, 8vw, 85px); height: clamp(65px, 8vw, 85px); border-radius: 50%; object-fit: cover; border: 3px solid rgba(250, 247, 242, 0.28); box-shadow: var(--shadow-md);" 
                     alt="{{ $portfolio->user->name }}" />
              @else
                <div style="font-family: var(--font-display); font-size: clamp(1.2rem, 1.5vw, 1.8rem); font-weight: 800; color: #1A0E08; background: var(--gradient-brand); width: clamp(65px, 8vw, 85px); height: clamp(65px, 8vw, 85px); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(250, 247, 242, 0.28); box-shadow: var(--shadow-md);">
                    {{ strtoupper(substr($portfolio->user->name, 0, 2)) }}
                </div>
                <!-- Hidden fallback img tag so bsa-intro.js reads logo source correctly -->
                <img class="avatar-icon" src="{{ asset('Matrials/empty-avatar.jpg') }}" style="display:none;" />
              @endif
              <h1 style="margin: 0; font-size: clamp(2.15rem, 1.45rem + 3.2vw, 3.95rem); line-height: 1.1; letter-spacing: 0.005em;">{{ $portfolio->user->name }}</h1>
            </div>
            <p class="hero-text">
              {{ $portfolio->hero_prefix ?? 'A results-driven' }}
              <strong><span class="typed-role" id="typed-role">{{ $portfolio->headline ?? 'Graduate' }}</span></strong>
              {{ $portfolio->hero_suffix ?? 'passionate about building products, crafting solutions, and continuous professional growth.' }}
            </p>
            <div class="hero-actions">
              @if($portfolio->projects->count())
                <a href="#projects" class="btn btn-primary">View My Work</a>
              @endif
              @if($portfolio->cv_path)
                <a href="{{ route('portfolio.cv.download', $portfolio->slug) }}" class="btn btn-outline" id="download-cv-btn-hero">
                    <i class="fas fa-file-pdf" style="color:var(--color-primary); margin-right: 0.35rem;"></i> Download CV
                </a>
              @endif
              <a href="#contact" class="btn btn-outline">Get In Touch</a>
            </div>
          </div>
          
          <div class="hero-cards">
            <article class="stat-card">
              <h3>{{ $portfolio->projects->count() }}+</h3>
              <p>Projects Completed</p>
            </article>
            <article class="stat-card">
              <h3>{{ $portfolio->skills->count() }}+</h3>
              <p>Skills Registered</p>
            </article>
            <article class="stat-card">
              <h3>{{ $portfolio->totalViews() }}+</h3>
              <p>Profile Views</p>
            </article>
          </div>
        </div>
      </section>

      <!-- ── ABOUT SECTION ── -->
      @if($portfolio->bio)
      <section class="section" id="about">
        <div class="container" style="max-width: 800px; text-align: center; margin: 0 auto;">
          <h2 class="section-title" style="margin-bottom: var(--space-6);">About Me</h2>
          <div class="about-content" style="text-align: center; display: flex; justify-content: center;">
            <p style="white-space: pre-line; color: #D4B89A; font-size: 1.1rem; line-height: 1.8; max-width: 66ch; margin: 0 auto; text-align: center;">{{ $portfolio->bio }}</p>
          </div>
        </div>
      </section>
      @endif

      <!-- ── SKILLS SECTION ── -->
      @if($portfolio->skills->count())
      @php
        $skillsByLevel = $portfolio->skills->groupBy('level');
      @endphp
      <section class="section section-alt" id="skills">
        <div class="container">
          <h2 class="section-title">My Skills</h2>
          <div class="skills-grid">
            @foreach(['expert', 'advanced', 'intermediate', 'beginner'] as $level)
                @if(isset($skillsByLevel[$level]) && $skillsByLevel[$level]->count() > 0)
                <article class="skill-card">
                  <h3 style="color: {{ $skillsByLevel[$level]->first()->levelColor() }}">{{ ucfirst($level) }} Skills</h3>
                  <ul>
                    @foreach($skillsByLevel[$level] as $skill)
                        <li>{{ $skill->name }}</li>
                    @endforeach
                  </ul>
                </article>
                @endif
            @endforeach
          </div>
        </div>
      </section>
      @endif

      <!-- ── PROJECTS SECTION ── -->
      @if($portfolio->projects->count())
      <section class="section" id="projects">
        <div class="container">
          <h2 class="section-title">Projects</h2>
          <div class="projects-grid">
            @foreach($portfolio->projects as $project)
            <article class="project-card" id="project-{{ $project->id }}">
              @php
                  // Gather project images list (first cover, then gallery)
                  $gallery = collect();
                  if ($project->cover_image_path) {
                      $gallery->push(['url' => $project->coverUrl(), 'caption' => $project->title]);
                  }
                  foreach ($project->galleryImages as $img) {
                      if ($project->cover_image_path && $img->image_path === $project->cover_image_path) {
                          continue;
                      }
                      $gallery->push(['url' => $img->url(), 'caption' => $img->caption ?? $img->alt_text ?? $project->title]);
                  }
              @endphp

              <!-- Project Cover thumbnail -->
              @if($project->coverUrl())
                <div class="project-thumb" style="background-image: url('{{ $project->coverUrl() }}'); background-size: cover; background-position: center; min-height: 180px; cursor: pointer; position: relative;" onclick="openLightbox({{ $gallery->toJson() }})">
                    @if($gallery->count() > 1)
                        <span style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.65); color: #fff; font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: var(--radius-sm); font-weight: 600; display: flex; align-items: center; gap: 0.3rem; pointer-events: none;">
                            <i class="fas fa-images"></i> {{ $gallery->count() }}
                        </span>
                    @endif
                </div>
              @else
                <div class="project-thumb" style="cursor: pointer;" onclick="openLightbox({{ $gallery->toJson() }})">
                    {{ $project->title }}
                </div>
              @endif

              <h3>{{ $project->title }}</h3>
              <p>{{ Str::limit($project->description, 130) }}</p>

              @if($project->tech_stack)
                  <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; padding: 0 var(--space-5) var(--space-3); margin-top: -0.5rem;">
                      @foreach($project->techStackArray() as $tech)
                          <span style="background: rgba(196, 120, 58, 0.08); border: 1px solid rgba(196, 120, 58, 0.15); color: var(--color-primary); font-size: 0.68rem; font-weight: 600; padding: 0.1rem 0.5rem; border-radius: var(--radius-pill);">{{ $tech }}</span>
                      @endforeach
                  </div>
              @endif

              @if($project->pdfFiles->count() > 0)
                  <div style="padding: 0 var(--space-5) var(--space-3); display: flex; flex-direction: column; gap: 0.3rem;">
                      @foreach($project->pdfFiles as $pdf)
                          <a href="{{ $pdf->url() }}" target="_blank" style="font-size: 0.78rem; color: var(--color-text-secondary); display: inline-flex; align-items: center; gap: 0.35rem;">
                              <i class="fas fa-file-pdf" style="color:var(--color-primary);"></i> {{ Str::limit($pdf->caption ?? 'Attachment PDF', 26) }}
                          </a>
                      @endforeach
                  </div>
              @endif

              <div class="project-links">
                @if($project->project_url)
                    <a href="{{ $project->project_url }}" target="_blank" rel="noopener">Live Demo</a>
                @endif
                @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener">GitHub</a>
                @endif
                @if($gallery->count() > 0 && !$project->project_url && !$project->github_url)
                    <a href="javascript:void(0)" onclick="openLightbox({{ $gallery->toJson() }})">View Images</a>
                @endif
              </div>
            </article>
            @endforeach
          </div>
        </div>
      </section>
      @endif

      <!-- ── CONTACT SECTION ── -->
      <section class="section section-alt" id="contact">
        <div class="container">
          <h2 class="section-title">Contact Me</h2>
          <p class="contact-text">
            Find me on these platforms and contact me directly. Only added channels will show below.
          </p>
          <div class="contact-links">
            <!-- Account primary email (Always available) -->
            <a class="contact-link-card" href="mailto:{{ $portfolio->user->email }}">
              <span class="contact-link-label">Gmail</span>
              <span class="contact-link-value">{{ $portfolio->user->email }}</span>
            </a>

            <!-- Dynamic platforms added/removed by the graduate -->
            @foreach($portfolio->socialLinks as $link)
                @php
                    $href = $link->url;
                    $displayValue = $link->url;
                    
                    // Format special schemes and clean displays
                    if (strtolower($link->platform) === 'gmail' && !str_starts_with($link->url, 'mailto:')) {
                        $href = 'mailto:' . $link->url;
                    } elseif (strtolower($link->platform) === 'phone') {
                        if (!str_starts_with($link->url, 'tel:')) {
                            $href = 'tel:' . preg_replace('/[^0-9+]/', '', $link->url);
                        }
                    } elseif (strtolower($link->platform) === 'whatsapp') {
                        $clean = preg_replace('/[^0-9]/', '', $link->url);
                        $href = 'https://wa.me/' . $clean;
                    }
                    
                    // Clean URL for nicer display
                    if (str_starts_with($displayValue, 'https://')) {
                        $displayValue = substr($displayValue, 8);
                    } elseif (str_starts_with($displayValue, 'http://')) {
                        $displayValue = substr($displayValue, 7);
                    }
                    if (str_starts_with($displayValue, 'www.')) {
                        $displayValue = substr($displayValue, 4);
                    }
                @endphp
                <a class="contact-link-card" href="{{ $href }}" target="_blank" rel="noopener noreferrer">
                  <span class="contact-link-label">{{ ucfirst($link->platform) }}</span>
                  <span class="contact-link-value">{{ Str::limit($displayValue, 32) }}</span>
                </a>
            @endforeach
          </div>
        </div>
      </section>

    </main>

    <!-- ── FOOTER ── -->
    <footer class="footer">
      <div class="container footer-wrap">
        <p>© <span id="year"></span> <a href="https://b-s-a.co/ar/" target="_blank">BSA</a> . All rights reserved.</p>
        <a href="#top" class="to-top">Back to top ↑</a>
      </div>
    </footer>

    <!-- ── LIGHTBOX GALLERY MODAL ── -->
    <div class="gallery-modal-v3" id="gallery-lightbox">
        <div class="gallery-modal-container">
            <button class="gallery-close-btn" onclick="closeLightbox()">&times;</button>
            
            <div class="gallery-active-image-wrap">
                <button class="gallery-nav-arrow arrow-prev" onclick="changeImage(-1)">&lsaquo;</button>
                <img src="" alt="" class="gallery-active-img" id="lightbox-img">
                <button class="gallery-nav-arrow arrow-next" onclick="changeImage(1)">&rsaquo;</button>
            </div>
            
            <div class="gallery-modal-caption" id="lightbox-caption"></div>
            <div class="gallery-modal-counter" id="lightbox-counter"></div>
        </div>
    </div>

    <!-- ── JAVASCRIPTS ── -->
    <!-- Passing dynamic roles array from DB to the typed animation global variable -->
    <script>
        @php
            $headlineRoles = array_filter(array_map('trim', explode(',', $portfolio->headline ?? '')));
            if (empty($headlineRoles)) {
                $headlineRoles = ['BSA Graduate'];
            }
        @endphp
        window.bsaRoles = {!! json_encode(array_values($headlineRoles)) !!};
    </script>

    <!-- Load Three.js library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // ── Three.js WebGL Particle Wave inside Hero ──
        (function initThreeAnimation() {
            const canvas = document.getElementById('hero-3d-canvas');
            if (!canvas) return;

            if (typeof THREE === 'undefined') {
                console.warn('Three.js failed to load. Falling back.');
                return;
            }

            try {
                const container = canvas.parentElement;
                let width = container.clientWidth;
                let height = container.clientHeight;

                const scene = new THREE.Scene();

                // Set up camera
                const camera = new THREE.PerspectiveCamera(65, width / height, 1, 1000);
                camera.position.z = 230;
                camera.position.y = 100;
                camera.lookAt(new THREE.Vector3(0, 0, 0));

                const renderer = new THREE.WebGLRenderer({
                    canvas: canvas,
                    alpha: true,
                    antialias: true
                });
                renderer.setSize(width, height);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

                const countX = 45;
                const countY = 30;
                const separation = 15;
                const totalParticles = countX * countY;

                const positions = new Float32Array(totalParticles * 3);
                const initialY = new Float32Array(totalParticles);

                let i = 0;
                for (let ix = 0; ix < countX; ix++) {
                    for (let iy = 0; iy < countY; iy++) {
                        positions[i] = (ix * separation) - ((countX * separation) / 2); // X
                        positions[i + 1] = 0; // Y
                        positions[i + 2] = (iy * separation) - ((countY * separation) / 2); // Z
                        initialY[i/3] = 0;
                        i += 3;
                    }
                }

                const geometry = new THREE.BufferGeometry();
                geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

                const isLightMode = document.body.classList.contains('light-mode');
                const particleColor = isLightMode ? 0xc4783a : 0xd4914e;

                const material = new THREE.PointsMaterial({
                    color: particleColor,
                    size: 2.3,
                    transparent: true,
                    opacity: isLightMode ? 0.38 : 0.65,
                    blending: THREE.AdditiveBlending
                });

                const particles = new THREE.Points(geometry, material);
                scene.add(particles);

                let targetMouseX = 0;
                let targetMouseY = 0;
                let currentMouseX = 0;
                let currentMouseY = 0;

                window.addEventListener('mousemove', (event) => {
                    targetMouseX = (event.clientX / window.innerWidth) * 2 - 1;
                    targetMouseY = -(event.clientY / window.innerHeight) * 2 + 1;
                });

                // Listen to class changes on body to swap colors on theme toggle
                const observer = new MutationObserver(() => {
                    const isLight = document.body.classList.contains('light-mode');
                    material.color.setHex(isLight ? 0xc4783a : 0xd4914e);
                    material.opacity = isLight ? 0.38 : 0.65;
                });
                observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

                window.addEventListener('resize', () => {
                    width = container.clientWidth;
                    height = container.clientHeight;
                    camera.aspect = width / height;
                    camera.updateProjectionMatrix();
                    renderer.setSize(width, height);
                }, { passive: true });

                let step = 0;
                function tick() {
                    requestAnimationFrame(tick);
                    
                    step += 0.015;
                    const positionAttribute = geometry.getAttribute('position');
                    const array = positionAttribute.array;

                    let index = 0;
                    for (let ix = 0; ix < countX; ix++) {
                        for (let iy = 0; iy < countY; iy++) {
                            const waveY = (Math.sin((ix + step * 9) * 0.18) * 16) +
                                          (Math.sin((iy + step * 7) * 0.22) * 12);
                            array[index + 1] = waveY - 35; // Position the wave slightly down
                            index += 3;
                        }
                    }
                    positionAttribute.needsUpdate = true;

                    currentMouseX += (targetMouseX - currentMouseX) * 0.05;
                    currentMouseY += (targetMouseY - currentMouseY) * 0.05;

                    particles.rotation.y = currentMouseX * 0.15;
                    particles.rotation.x = (currentMouseY * 0.1) + 0.08;

                    renderer.render(scene, camera);
                }
                
                tick();

            } catch (err) {
                console.error(err);
            }
        })();

        // ── Lightbox Gallery Modal Logic ──
        let currentGallery = [];
        let currentImageIndex = 0;
        const lightboxModal = document.getElementById('gallery-lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxCounter = document.getElementById('lightbox-counter');

        window.openLightbox = function(imagesArray) {
            if (!imagesArray || !imagesArray.length) return;
            currentGallery = imagesArray;
            currentImageIndex = 0;
            
            updateLightboxImage();
            lightboxModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        window.closeLightbox = function() {
            lightboxModal.classList.remove('active');
            document.body.style.overflow = '';
        };

        window.changeImage = function(direction) {
            if (!currentGallery.length) return;
            currentImageIndex = (currentImageIndex + direction + currentGallery.length) % currentGallery.length;
            updateLightboxImage();
        };

        function updateLightboxImage() {
            if (!currentGallery[currentImageIndex]) return;
            const item = currentGallery[currentImageIndex];
            
            lightboxImg.src = item.url;
            lightboxImg.alt = item.caption || '';
            lightboxCaption.textContent = item.caption || '';
            lightboxCounter.textContent = `${currentImageIndex + 1} / ${currentGallery.length}`;
        }

        window.addEventListener('keydown', (e) => {
            if (!lightboxModal.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') changeImage(1);
            if (e.key === 'ArrowLeft') changeImage(-1);
        });

        lightboxModal.addEventListener('click', (e) => {
            if (e.target === lightboxModal) closeLightbox();
        });
    </script>

    <!-- Project Click Tracking Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.project-card').forEach(card => {
                const projectId = card.id.replace('project-', '');
                
                card.querySelectorAll('a, .project-thumb').forEach(el => {
                    el.addEventListener('click', () => {
                        fetch(`/projects/${projectId}/click`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        }).catch(err => console.error('Error tracking click:', err));
                    });
                });
            });
        });
    </script>

    <!-- Original BSA Scripts -->
    <script src="{{ asset('js/bsa-portfolio/index.js') }}"></script>
  </body>
</html>
