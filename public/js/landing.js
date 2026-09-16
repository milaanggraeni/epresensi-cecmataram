// Initialize AOS (Animate On Scroll)
document.addEventListener("DOMContentLoaded", function () {
    AOS.init({
        duration: 800,
        easing: "ease-in-out",
        once: true,
        offset: 100,
    });

    // Initialize all functions
    initCursorGlow();
    initNavbarScroll();
    initMobileMenu();
    initParticles();
    initCounterAnimation();
    initBackToTop();
    initSmoothScroll();
});

// ===== Cursor Glow Effect =====
function initCursorGlow() {
    const cursorGlow = document.querySelector(".cursor-glow");

    document.addEventListener("mousemove", (e) => {
        cursorGlow.style.left = e.clientX - 25 + "px";
        cursorGlow.style.top = e.clientY - 25 + "px";
    });
}

// ===== Navbar Scroll Effect =====
function initNavbarScroll() {
    const navbar = document.querySelector(".navbar-container");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
}

// ===== Mobile Menu Toggle =====
function initMobileMenu() {
    const menuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const links = mobileMenu.querySelectorAll("a");

    menuBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
    });

    links.forEach((link) => {
        link.addEventListener("click", () => {
            mobileMenu.classList.add("hidden");
        });
    });

    // Close menu when clicking outside
    document.addEventListener("click", (e) => {
        if (!menuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.add("hidden");
        }
    });
}

// ===== Particle Canvas Animation =====
function initParticles() {
    const canvas = document.getElementById("particle-canvas");
    const ctx = canvas.getContext("2d");

    // Set canvas size
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    // Particle array
    const particles = [];
    const particleCount = 50;

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 2 + 0.5;
            this.speedX = Math.random() * 0.5 - 0.25;
            this.speedY = Math.random() * 0.5 - 0.25;
            this.opacity = Math.random() * 0.5 + 0.2;
        }

        update() {
            this.x += this.speedX;
            this.y += this.speedY;

            // Wrap around screen
            if (this.x > canvas.width) this.x = 0;
            if (this.x < 0) this.x = canvas.width;
            if (this.y > canvas.height) this.y = 0;
            if (this.y < 0) this.y = canvas.height;

            // Pulse opacity
            this.opacity += Math.random() * 0.1 - 0.05;
            this.opacity = Math.max(0.1, Math.min(0.7, this.opacity));
        }

        draw() {
            ctx.fillStyle = `rgba(255, 107, 0, ${this.opacity})`;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    // Create particles
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach((particle) => {
            particle.update();
            particle.draw();
        });

        // Draw connecting lines
        particles.forEach((particle, index) => {
            for (let j = index + 1; j < particles.length; j++) {
                const dx = particles[j].x - particle.x;
                const dy = particles[j].y - particle.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 150) {
                    ctx.strokeStyle = `rgba(255, 107, 0, ${0.1 * (1 - distance / 150)})`;
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(particle.x, particle.y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        });

        requestAnimationFrame(animate);
    }

    animate();

    // Handle window resize
    window.addEventListener("resize", () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

// ===== Counter Animation =====
function initCounterAnimation() {
    const counters = document.querySelectorAll(".counter");
    const speed = 200; // milliseconds per increment

    const observerOptions = {
        threshold: 0.5,
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (
                entry.isIntersecting &&
                !entry.target.classList.contains("counted")
            ) {
                animateCounter(entry.target);
                entry.target.classList.add("counted");
            }
        });
    }, observerOptions);

    counters.forEach((counter) => {
        observer.observe(counter);
    });

    function animateCounter(element) {
        const target = parseInt(element.dataset.target);
        let count = 0;

        const increment = Math.ceil(target / (speed / 10));

        const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = count;
            }
        }, 10);
    }
}

// ===== Back to Top Button =====
function initBackToTop() {
    const backToTopBtn = document.getElementById("back-to-top");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add("show");
        } else {
            backToTopBtn.classList.remove("show");
        }
    });

    backToTopBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });
}

// ===== Smooth Scroll =====
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();

            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });
}

// ===== Form Submission Handler =====
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Show success message (in real app, this would be a server response)
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = "✓ Pesan Terkirim!";
            submitBtn.classList.add("bg-green-600");

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.classList.remove("bg-green-600");
                form.reset();
            }, 3000);

            console.log("Form Data:", data);
        });
    }
});

// ===== Button Ripple Effect =====
document.querySelectorAll(".btn-primary, .btn-secondary").forEach((button) => {
    button.addEventListener("click", function (e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement("span");
        ripple.style.position = "absolute";
        ripple.style.left = x + "px";
        ripple.style.top = y + "px";
        ripple.style.width = "20px";
        ripple.style.height = "20px";
        ripple.style.background = "rgba(255, 255, 255, 0.6)";
        ripple.style.borderRadius = "50%";
        ripple.style.transform = "scale(0)";
        ripple.style.animation = "ripple-animation 0.6s ease-out";
        ripple.style.pointerEvents = "none";

        this.style.position = "relative";
        this.style.overflow = "hidden";
        this.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple animation to CSS
const style = document.createElement("style");
style.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ===== Neon Text Hover Effect =====
document.querySelectorAll("h1, h2, h3").forEach((heading) => {
    heading.addEventListener("mouseenter", function () {
        this.style.textShadow =
            "0 0 20px rgba(255, 107, 0, 0.8), 0 0 40px rgba(255, 214, 0, 0.6)";
    });

    heading.addEventListener("mouseleave", function () {
        this.style.textShadow = "none";
    });
});

// ===== Feature Card Hover Animation =====
document.querySelectorAll(".feature-card").forEach((card) => {
    card.addEventListener("mouseenter", function () {
        this.style.transform = "translateY(-15px) scale(1.02)";
        this.style.boxShadow = "0 30px 60px rgba(193, 18, 31, 0.4)";
    });

    card.addEventListener("mouseleave", function () {
        this.style.transform = "translateY(0) scale(1)";
        this.style.boxShadow = "none";
    });
});

// ===== Gallery Hover Zoom =====
document.querySelectorAll(".gallery-item").forEach((item) => {
    const img = item.querySelector("img");

    item.addEventListener("mouseenter", function () {
        if (img) {
            img.style.transform = "scale(1.15) rotate(2deg)";
        }
    });

    item.addEventListener("mouseleave", function () {
        if (img) {
            img.style.transform = "scale(1) rotate(0deg)";
        }
    });
});

// ===== Lazy Loading Images =====
if ("IntersectionObserver" in window) {
    const images = document.querySelectorAll("img[data-lazy]");

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.lazy;
                img.removeAttribute("data-lazy");
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach((img) => imageObserver.observe(img));
}

// ===== Performance Optimization =====
// Debounce scroll events
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Throttle resize events
function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

// ===== Accessibility =====
// Ensure keyboard navigation works
document.querySelectorAll("button, a").forEach((element) => {
    element.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            element.click();
        }
    });
});

console.log("Landing page initialized successfully!");
