const menuButton = document.querySelector(".menu-btn");
const nav = document.querySelector(".nav");
const navLinks = document.querySelectorAll(".nav-list a");
const yearEl = document.getElementById("year");
const typedRole = document.getElementById("typed-role");

if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
}

if (menuButton && nav) {
  menuButton.addEventListener("click", () => {
    nav.classList.toggle("open");
    const isExpanded = nav.classList.contains("open");
    menuButton.setAttribute("aria-expanded", String(isExpanded));
  });
}

for (const link of navLinks) {
  link.addEventListener("click", () => {
    nav?.classList.remove("open");
    menuButton?.setAttribute("aria-expanded", "false");
  });
}

if (typedRole) {
  const roles = window.bsaRoles || ["Digital Markter", "Media Buyer", "Account Manger "];
  let roleIndex = 0;
  let charIndex = 0;
  let isDeleting = false;

  const typeRole = () => {
    const currentRole = roles[roleIndex];

    if (isDeleting) {
      charIndex -= 1;
    } else {
      charIndex += 1;
    }

    typedRole.textContent = currentRole.slice(0, charIndex);

    let delay = isDeleting ? 55 : 95;

    if (!isDeleting && charIndex === currentRole.length) {
      delay = 1300;
      isDeleting = true;
    } else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      roleIndex = (roleIndex + 1) % roles.length;
      delay = 350;
    }

    setTimeout(typeRole, delay);
  };

  typeRole();
}

const revealTargets = document.querySelectorAll(
  ".hero-content, .hero-cards, .section-title, .about-grid, .skill-card, .project-card, .contact-text, .contact-links, .contact-link-card, .stat-card",
);

revealTargets.forEach((element, index) => {
  element.classList.add("reveal");
  element.style.setProperty("--delay", `${(index % 4) * 90}ms`);
});

if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries, currentObserver) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in-view");
          currentObserver.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.15,
      rootMargin: "0px 0px -40px 0px",
    },
  );

  revealTargets.forEach((element) => observer.observe(element));
} else {
  revealTargets.forEach((element) => element.classList.add("in-view"));
}
// v2
// ── Theme toggle ──
const themeToggle = document.getElementById("theme-toggle");
const themeIcon = themeToggle?.querySelector(".theme-icon");

const applyTheme = (isDark) => {
  document.body.classList.toggle("light-mode", !isDark);
  if (themeIcon) themeIcon.textContent = isDark ? "☀️" : "🌙";
  localStorage.setItem("theme", isDark ? "dark" : "light");
};

// Load saved preference
const savedTheme = localStorage.getItem("theme");
const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
const startDark = savedTheme ? savedTheme === "dark" : prefersDark;
applyTheme(startDark);

themeToggle?.addEventListener("click", () => {
  const isCurrentlyDark = !document.body.classList.contains("light-mode");
  applyTheme(!isCurrentlyDark);
});