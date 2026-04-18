// Subtle Parallax Effect for Hero Image
window.addEventListener('scroll', function() {
    requestAnimationFrame(() => {
        const scrolled = window.scrollY;
        const parallaxImage = document.querySelector('.parallax-bg');
        if (parallaxImage && scrolled < 1000) {
            parallaxImage.style.transform = `translateY(calc(-50% + ${scrolled * 0.15}px))`;
        }
    });
}, { passive: true });
