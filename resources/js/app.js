import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Scroll reveal.
 *
 * Elements marked [data-reveal] start displaced in CSS and get
 * [data-revealed] once they enter the viewport, which is also what
 * triggers the STEM traces and the drawn rules nested inside them.
 *
 * If the reader prefers reduced motion, or the browser has no
 * IntersectionObserver, everything is revealed immediately. The CSS
 * already renders the finished state in that case, so this is only
 * making sure nothing is left invisible.
 */
const revealAll = (elements) => {
    elements.forEach((element) => element.setAttribute('data-revealed', ''));
};

const setupReveal = () => {
    const elements = Array.from(document.querySelectorAll('[data-reveal]'));

    if (elements.length === 0) {
        return;
    }

    const prefersReducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        revealAll(elements);

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.setAttribute('data-revealed', '');

                // Reveal is one way. Scrolling back up should not replay it.
                observer.unobserve(entry.target);
            });
        },
        {
            // Fire a little before the element is fully on screen, so the
            // motion finishes about when the reader arrives at it.
            rootMargin: '0px 0px -12% 0px',
            threshold: 0.15,
        },
    );

    elements.forEach((element) => observer.observe(element));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupReveal);
} else {
    setupReveal();
}
