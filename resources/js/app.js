import Alpine from 'alpinejs';

/**
 * The hero slideshow.
 *
 * Advances on a timer, pauses while a pointer or the keyboard is on it, and
 * does not advance at all for a reader who has asked for reduced motion: they
 * get the first photograph as a still. The headline carries all the meaning,
 * so the slides themselves are decorative and hidden from assistive tech; the
 * controls are the only part exposed, for anyone who wants to look through
 * them deliberately.
 */
Alpine.data('heroSlider', (count = 0, interval = 6000) => ({
    count,
    interval,
    active: 0,
    timer: null,

    init() {
        if (this.count < 2) {
            return;
        }

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

        if (reducedMotion.matches) {
            return;
        }

        this.start();

        // Someone can turn reduced motion on while the page is open.
        reducedMotion.addEventListener('change', (event) => {
            if (event.matches) {
                this.stop();
                this.active = 0;
            } else {
                this.start();
            }
        });

        // A slideshow running in a background tab is wasted work.
        document.addEventListener('visibilitychange', () => {
            document.hidden ? this.stop() : this.start();
        });
    },

    start() {
        this.stop();
        this.timer = setInterval(() => this.next(), this.interval);
    },

    stop() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    },

    next() {
        this.active = (this.active + 1) % this.count;
    },

    goTo(index) {
        this.active = index;

        // Restart the clock so a deliberate choice gets a full turn on screen.
        if (this.timer) {
            this.start();
        }
    },
}));

/**
 * The word that cycles at the end of a line.
 *
 * Holds still for anyone who has asked for reduced motion, and stops entirely
 * in a background tab. Every word is already in the markup, so this only ever
 * changes which one is visible.
 */
Alpine.data('rotatingWord', (count = 0, interval = 2600) => ({
    count,
    interval,
    active: 0,
    timer: null,

    init() {
        if (this.count < 2) {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        this.start();

        document.addEventListener('visibilitychange', () => {
            document.hidden ? this.stop() : this.start();
        });
    },

    start() {
        this.stop();
        this.timer = setInterval(() => {
            this.active = (this.active + 1) % this.count;
        }, this.interval);
    },

    stop() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    },
}));

/**
 * A number that counts up to its value the first time it is scrolled to.
 * The final value is already in the markup, so nothing depends on this
 * running: it only animates a number that is there either way.
 */
Alpine.data('countUp', (raw = '') => ({
    raw,
    display: raw,

    init() {
        const match = String(this.raw).match(/^(\D*)(\d[\d,.]*)(.*)$/);

        if (! match || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const [, prefix, digits, suffix] = match;
        const target = Number(digits.replace(/[,\s]/g, ''));

        if (! Number.isFinite(target) || target === 0) {
            return;
        }

        if (! ('IntersectionObserver' in window)) {
            return;
        }

        this.display = prefix + '0' + suffix;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                observer.unobserve(entry.target);

                const started = performance.now();
                const duration = 1400;

                const step = (now) => {
                    const progress = Math.min(1, (now - started) / duration);
                    // Ease out, so it settles rather than stops dead.
                    const eased = 1 - Math.pow(1 - progress, 3);

                    this.display = prefix + Math.round(target * eased).toLocaleString() + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                requestAnimationFrame(step);
            });
        }, { threshold: 0.4 });

        observer.observe(this.$el);
    },
}));

window.Alpine = Alpine;

Alpine.start();

/**
 * Scroll reveal.
 *
 * Elements marked [data-reveal] start displaced in CSS and get
 * [data-revealed] once they enter the viewport, which is also what
 * triggers the board traces and the drawn rules nested inside them.
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
