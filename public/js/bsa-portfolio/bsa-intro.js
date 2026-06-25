/* ============================================================
   BSA Academy — Folder Intro Animation  v7 (Clean Intro Only)
   ============================================================ */
(function () {

  // Load fonts
  const fl = document.createElement("link");
  fl.rel = "stylesheet";
  fl.href = "https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap";
  document.head.appendChild(fl);

  // Inject styles with GPU acceleration + full responsiveness
  const s = document.createElement("style");
  s.textContent = `
    body.bsa-active{overflow:hidden;}
    #bsa-ov{
      position:fixed;inset:0;z-index:99999;
      background:#1A0E08;
      display:flex;flex-direction:column;
      align-items:center;justify-content:center;
      overflow:hidden;font-family:'DM Sans',sans-serif;
      transform-origin:center center;
      will-change:transform,opacity;
      backface-visibility:hidden;
      transform:translateZ(0);
    }
    #bsa-ov::before{
      content:'';position:absolute;inset:0;pointer-events:none;
      background:
        radial-gradient(ellipse 600px 400px at 15% 20%, rgba(196,120,58,0.13) 0%, transparent 60%),
        radial-gradient(ellipse 500px 350px at 85% 80%, rgba(212,145,78,0.1) 0%, transparent 60%);
    }
    #bsa-ov::after{
      content:'';
      position:absolute;inset:0;
      background-image:url('Matrials/BSA-icon-with-out-bg.png');
      background-repeat:no-repeat;
      background-position:center center;
      background-size:50%;
      opacity:0.2;pointer-events:none;z-index:1;
    }
    #bsa-prog{
      position:absolute;bottom:0;left:0;height:2px;width:0%;
      background:linear-gradient(90deg,#C4783A,#E8B88A);z-index:10;
    }
    #bsa-fw{
      position:relative;width:min(580px,90vw);
      perspective:1200px;z-index:2;
      backface-visibility:hidden;
      transform:translate3d(0,0,0);
    }
    .bsa-tab{
      width:130px;height:24px;
      background:linear-gradient(90deg,#3D1F0F,#2A1508);
      border-radius:7px 7px 0 0;margin-left:28px;
      border:1px solid rgba(196,120,58,0.3);border-bottom:none;
    }
    .bsa-body{
      width:100%;
      background:linear-gradient(160deg,#2C1A0E 0%,#1E0F07 55%,#160B05 100%);
      border:1px solid rgba(196,120,58,0.32);
      border-radius:2px 10px 10px 10px;
      padding:42px 42px 46px;position:relative;
      transform-origin:left center;
      transform-style:preserve-3d;
      will-change:transform,opacity;
      backface-visibility:hidden;
      box-shadow:0 4px 0 rgba(0,0,0,0.5),0 28px 80px rgba(0,0,0,0.68),inset 0 1px 0 rgba(196,120,58,0.08);
    }
    .bsa-body::before{
      content:'';position:absolute;top:6%;bottom:6%;left:50%;width:1px;
      background:linear-gradient(180deg,transparent,rgba(196,120,58,0.06) 30%,rgba(196,120,58,0.06) 70%,transparent);
    }
    .bsa-body::after{
      content:'';position:absolute;top:0;right:0;
      width:0;height:0;border-style:solid;border-width:0 26px 26px 0;
      border-color:transparent rgba(196,120,58,0.1) transparent transparent;border-radius:0 10px 0 0;
    }
    .bsa-grid{
      display:grid;grid-template-columns:auto 1fr;gap:30px;align-items:start;
      position:relative;z-index:2;
    }
    .bsa-left{display:flex;flex-direction:column;align-items:center;gap:12px;width:120px;}
    .bsa-mark{
      width:84px;height:84px;border-radius:50%;
      border:1.5px solid rgba(196,120,58,0.38);
      display:flex;align-items:center;justify-content:center;
      background:rgba(196,120,58,0.06);overflow:hidden;
      position:relative;z-index:3;
    }
    .bsa-mark img{width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 0 10px rgba(196,120,58,0.4));}
    .bsa-mark-txt{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:rgba(196,120,58,0.75);letter-spacing:0.02em;}
    .bsa-vline{width:1px;flex:1;min-height:60px;background:linear-gradient(180deg,transparent,rgba(196,120,58,0.22) 30%,rgba(196,120,58,0.22) 70%,transparent);}
    .bsa-name{
      font-size:12.5px;font-weight:500;letter-spacing:0.06em;
      color:rgba(212,145,78,0.9);text-align:center;
      word-break:break-word;line-height:1.3;
    }
    .bsa-right{display:flex;flex-direction:column;}
    .bsa-agency{font-size:8px;font-weight:500;letter-spacing:0.28em;text-transform:uppercase;color:rgba(196,120,58,0.4);margin-bottom:14px;}
    .bsa-title{font-family:'Cormorant Garamond',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#FAF7F2;line-height:1.15;margin-bottom:18px;}
    .bsa-rule{display:block;height:1px;background:linear-gradient(90deg,rgba(196,120,58,0.26),transparent);margin-bottom:18px;}
    .bsa-meta{display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;}
    .bsa-mi{display:flex;flex-direction:column;gap:2px;}
    .bsa-ml{font-size:7.5px;font-weight:600;letter-spacing:0.22em;text-transform:uppercase;color:rgba(156,123,101,0.46);}
    .bsa-mv{font-size:10.5px;color:rgba(250,247,242,0.46);}
    .bsa-foot{position:absolute;bottom:14px;right:18px;font-size:7.5px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(196,120,58,0.2);}
    .bsa-thread{
      position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
      width:140px;height:110px;pointer-events:none;z-index:1;
    }
    #bsa-ts{width:140px;height:110px;overflow:visible;}
    #bsa-hint{margin-top:30px;display:flex;flex-direction:column;align-items:center;gap:8px;z-index:2;transition:opacity 0.4s ease;}
    .bsa-ht{font-size:9.5px;font-weight:500;letter-spacing:0.22em;text-transform:uppercase;color:rgba(156,123,101,0.48);text-align:center;}
    .bsa-mouse{width:20px;height:32px;border:1.5px solid rgba(196,120,58,0.28);border-radius:10px;position:relative;}
    .bsa-whl{width:2.5px;height:6px;background:rgba(196,120,58,0.52);border-radius:2px;position:absolute;top:5px;left:50%;transform:translateX(-50%);animation:bsa-whl 1.8s ease infinite;}
    .bsa-arrs{display:flex;flex-direction:column;align-items:center;gap:1px;}
    .bsa-arrs i{display:block;width:7px;height:4px;border-right:1.5px solid rgba(196,120,58,0.28);border-bottom:1.5px solid rgba(196,120,58,0.28);transform:rotate(45deg);animation:bsa-arr 1.8s ease infinite;}
    .bsa-arrs i:nth-child(2){animation-delay:.15s;opacity:.5;}
    .bsa-arrs i:nth-child(3){animation-delay:.3s;opacity:.25;}
    @keyframes bsa-whl{0%{transform:translateX(-50%) translateY(0);opacity:1;}75%{transform:translateX(-50%) translateY(10px);opacity:0;}100%{transform:translateX(-50%) translateY(0);opacity:0;}}
    @keyframes bsa-arr{0%,100%{opacity:.5;transform:rotate(45deg) translateY(0);}50%{opacity:1;transform:rotate(45deg) translateY(2px);}}
    @keyframes bsa-rise{from{opacity:0;transform:translateY(36px) scale(0.97);}to{opacity:1;transform:translateY(0) scale(1);}}

    /* ── Mobile Responsive ── */
    @media (max-width: 480px) {
      #bsa-fw { width:min(390px,92vw); }
      .bsa-tab { width:100px;height:18px;margin-left:15px;border-radius:5px 5px 0 0; }
      .bsa-body { padding:24px 20px 28px;border-radius:2px 8px 8px 8px; }
      .bsa-body::before { display:none; }
      .bsa-grid { grid-template-columns:1fr;gap:15px;justify-items:center;text-align:center; }
      .bsa-left { gap:8px;width:100%; }
      .bsa-vline { display:none; }
      .bsa-mark { width:64px;height:64px; }
      .bsa-name { font-size:12px; }
      .bsa-right { align-items:center;width:100%; }
      .bsa-agency { margin-bottom:6px;font-size:7px; }
      .bsa-title { font-size:20px;margin-bottom:12px; }
      .bsa-rule {
        margin-bottom:12px;width:60%;margin-inline:auto;
        background:linear-gradient(90deg,transparent,rgba(196,120,58,0.26),transparent);
      }
      .bsa-meta { gap:6px 12px;width:100%; }
      .bsa-ml { font-size:7px; }
      .bsa-mv { font-size:9.5px; }
      .bsa-foot { bottom:8px;right:12px;font-size:6.5px; }
      .bsa-thread { width:115px;height:90px; }
      #bsa-ts { width:115px;height:90px; }
    }
    @media (max-height: 660px) {
      #bsa-fw { transform:scale(0.85); }
      #bsa-hint { margin-top:15px;gap:4px; }
    }
  `;
  document.head.appendChild(s);

  // Retrieve data from the portfolio page
  const logoSrc = (() => {
    const el = document.querySelector(".avatar-icon");
    return el ? el.src : "Matrials/empty-avatar.jpg";
  })();
  const nameEl = document.querySelector(".trainee-Name");
  const nameText = nameEl ? nameEl.textContent.trim() : "BSA Graduate";

  // Build overlay
  const ov = document.createElement("div");
  ov.id = "bsa-ov";
  ov.innerHTML = `
    <div id="bsa-prog"></div>
    <div id="bsa-fw" style="animation:bsa-rise 0.95s cubic-bezier(0.22,1,0.36,1) 0.35s both;">
      <div class="bsa-tab"></div>
      <div class="bsa-body" id="bsa-body">
        <div class="bsa-grid">
          <div class="bsa-left">
            <div class="bsa-mark">
              <img src="${logoSrc}" alt="BSA"
                onerror="this.style.display='none';this.parentNode.innerHTML='<span class=bsa-mark-txt>BSA</span>'"/>
            </div>
            <div class="bsa-name">${nameText}</div>
            <div class="bsa-vline"></div>
          </div>
          <div class="bsa-right">
            <div class="bsa-agency">Bibliotheca Scientiarum Agency</div>
            <div class="bsa-title">BSA Graduate Trainee<br>Portfolio</div>
            <span class="bsa-rule"></span>
            <div class="bsa-meta">
              <div class="bsa-mi"><span class="bsa-ml">Program</span><span class="bsa-mv">Graduate Trainee</span></div>
              <div class="bsa-mi"><span class="bsa-ml">Year</span><span class="bsa-mv">${new Date().getFullYear()}</span></div>
              <div class="bsa-mi"><span class="bsa-ml">Department</span><span class="bsa-mv">Digital & Web</span></div>
              <div class="bsa-mi"><span class="bsa-ml">Status</span><span class="bsa-mv">Active</span></div>
            </div>
          </div>
        </div>
        <div class="bsa-thread">
          <svg id="bsa-ts" viewBox="0 0 140 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="30"  cy="55" r="11" fill="#2A1810" stroke="rgba(196,120,58,0.5)" stroke-width="1.5"/>
            <circle cx="30"  cy="55" r="5.5" fill="rgba(196,120,58,0.16)" stroke="rgba(196,120,58,0.36)" stroke-width="1"/>
            <circle cx="110" cy="55" r="11" fill="#2A1810" stroke="rgba(196,120,58,0.5)" stroke-width="1.5"/>
            <circle cx="110" cy="55" r="5.5" fill="rgba(196,120,58,0.16)" stroke="rgba(196,120,58,0.36)" stroke-width="1"/>
            <path id="bsa-t1" d="M30,43 C30,43 70,28 110,43 C124,49 124,61 110,67 C70,82 30,67 30,67 C16,61 16,49 30,43 Z"
              stroke="rgba(196,120,58,0.72)" stroke-width="2" stroke-linecap="round" fill="none"/>
            <path id="bsa-t2" d="M42,55 C42,55 70,47 98,55 C106,58 106,62 98,65 C70,73 42,65 42,65 C34,62 34,58 42,55 Z"
              stroke="rgba(212,145,78,0.5)" stroke-width="1.4" stroke-linecap="round" fill="none"/>
            <path id="bsa-t3" d="M70,28 C70,22 73,15 77,10"
              stroke="rgba(196,120,58,0.4)" stroke-width="1.3" stroke-linecap="round" fill="none"/>
          </svg>
        </div>
        <span class="bsa-foot">BSA — Confidential</span>
      </div>
    </div>
    <div id="bsa-hint">
      <div class="bsa-mouse"><div class="bsa-whl"></div></div>
      <div class="bsa-arrs"><i></i><i></i><i></i></div>
      <div class="bsa-ht">Scroll to open — scroll up to close</div>
    </div>
  `;

  document.body.appendChild(ov);
  document.body.classList.add("bsa-active");

  const body  = document.getElementById("bsa-body");
  const hint  = document.getElementById("bsa-hint");
  const prog  = document.getElementById("bsa-prog");
  const t1    = document.getElementById("bsa-t1");
  const t2    = document.getElementById("bsa-t2");
  const t3    = document.getElementById("bsa-t3");

  // Init SVG dash arrays
  setTimeout(() => {
    [t1, t2, t3].forEach(p => {
      const l = p.getTotalLength();
      p.style.strokeDasharray = l;
      p.style.strokeDashoffset = 0;
    });
  }, 140);

  const MAX        = 380;
  const UNWIND_END = 0.35;
  const OPEN_START = 0.38;
  const OPEN_END   = 0.72;
  const ZOOM_START = 0.76;

  let acc  = 0;
  let done = false;

  function easeInOut(t) {
    return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
  }

  function update() {
    if (done) return;
    const p = acc / MAX;
    prog.style.width = p * 100 + "%";

    /* Phase 1 — Unwind thread */
    if (p <= UNWIND_END) {
      const t  = p / UNWIND_END;
      hint.style.opacity = String(Math.max(0, 1 - t * 2.5));
      const l1 = t1.getTotalLength ? t1.getTotalLength() : 260;
      const l2 = t2.getTotalLength ? t2.getTotalLength() : 180;
      const l3 = t3.getTotalLength ? t3.getTotalLength() : 40;
      t1.style.strokeDashoffset = String(l1 * Math.min(t * 1.1, 1));
      t2.style.strokeDashoffset = String(l2 * Math.min(Math.max((t - 0.12) * 1.3, 0), 1));
      t3.style.strokeDashoffset = String(l3 * Math.min(Math.max((t - 0.22) * 1.6, 0), 1));
      body.style.transform = "perspective(1100px) rotateY(0deg)";
      body.style.opacity   = "1";
      ov.style.transform   = "scale(1)";
      ov.style.opacity     = "1";
    }

    /* Phase 2 — Folder opens */
    if (p > OPEN_START && p <= OPEN_END) {
      const e = easeInOut((p - OPEN_START) / (OPEN_END - OPEN_START));
      body.style.transform = `perspective(1100px) rotateY(-${e * 100}deg)`;
      body.style.opacity   = String(1 - e * 0.55);
      ov.style.transform   = "scale(1)";
      ov.style.opacity     = "1";
    }

    /* Phase 3 — Zoom into page */
    if (p > ZOOM_START) {
      const e = easeInOut((p - ZOOM_START) / (1 - ZOOM_START));
      ov.style.transform = `scale(${1 + e * 1.5})`;
      ov.style.opacity   = String(1 - e);
      if (e >= 0.98) finish();
    } else if (p <= OPEN_START) {
      ov.style.transform = "scale(1)";
      ov.style.opacity   = "1";
    }

    /* Scroll UP — reset hint */
    if (p < 0.05) {
      hint.style.opacity = "1";
      [t1, t2, t3].forEach(path => { path.style.strokeDashoffset = "0"; });
      body.style.transform = "perspective(1100px) rotateY(0deg)";
      body.style.opacity   = "1";
    }
  }

  function finish() {
    done = true;
    ov.remove();
    document.body.classList.remove("bsa-active");
  }

  function addDelta(d) {
    acc = Math.max(0, Math.min(MAX, acc + d));
    update();
  }

  // Mouse wheel
  ov.addEventListener("wheel", e => {
    e.preventDefault();
    addDelta(e.deltaY * 1.25);
  }, { passive: false });

  // Touch
  let ty = 0;
  ov.addEventListener("touchstart", e => { ty = e.touches[0].clientY; }, { passive: true });
  ov.addEventListener("touchmove", e => {
    const dy = ty - e.touches[0].clientY;
    ty = e.touches[0].clientY;
    addDelta(dy * 3.5);
    e.preventDefault();
  }, { passive: false });

  // Keyboard
  document.addEventListener("keydown", e => {
    if (["ArrowDown", "PageDown", "Space"].includes(e.code)) { e.preventDefault(); addDelta(55); }
    if (["ArrowUp",  "PageUp"].includes(e.code))             { e.preventDefault(); addDelta(-55); }
  });

  update();
})();
