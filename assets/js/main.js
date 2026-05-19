document.addEventListener('DOMContentLoaded', () => {
    // Intersection Observer for Scroll Animations (Reveal)
    const reveals = document.querySelectorAll('.reveal');

    const revealOnScroll = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    });

    reveals.forEach(reveal => {
        revealOnScroll.observe(reveal);
    });

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Theme Switcher Logic
    const themeToggle = document.querySelector('.mode-icon');
    const currentTheme = localStorage.getItem('theme') || 'dark';

    // Apply the active theme on load
    if (currentTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        if (themeToggle) {
            themeToggle.classList.replace('fa-moon', 'fa-sun');
        }
    }

    if (themeToggle) {
        themeToggle.style.cursor = 'pointer';
        themeToggle.addEventListener('click', () => {
            let theme = 'dark';
            if (document.documentElement.getAttribute('data-theme') !== 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
                themeToggle.classList.replace('fa-moon', 'fa-sun');
                theme = 'light';
            } else {
                document.documentElement.removeAttribute('data-theme');
                themeToggle.classList.replace('fa-sun', 'fa-moon');
            }
            localStorage.setItem('theme', theme);
        });
    }
});
