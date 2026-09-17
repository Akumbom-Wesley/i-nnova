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

/**
 * Light and dark.
 *
 * Three states: following the system, forced light, forced dark. The choice
 * is written to the same key the inline script in the document head reads, so
 * a reload paints the right mode immediately rather than flashing the other
 * one first.
 */
Alpine.data('themeToggle', () => ({
    mode: 'system',

    init() {
        this.mode = window.localStorage.getItem('theme') || 'system';
        this.apply();
    },

    get label() {
        return {
            system: 'Appearance: following your system. Switch to light.',
            light: 'Appearance: light. Switch to dark.',
            dark: 'Appearance: dark. Follow your system instead.',
        }[this.mode];
    },

    cycle() {
        this.mode = { system: 'light', light: 'dark', dark: 'system' }[this.mode];
        this.apply();
    },

    apply() {
        const root = document.documentElement;

        if (this.mode === 'system') {
            root.removeAttribute('data-theme');

            try {
                window.localStorage.removeItem('theme');
            } catch (error) {
                // Private browsing can refuse storage. The mode still applies
                // for this page; it just will not be remembered.
            }

            return;
        }

        root.setAttribute('data-theme', this.mode);

        try {
            window.localStorage.setItem('theme', this.mode);
        } catch (error) {
            // As above.
        }
    },
}));

/**
 * The gallery lightbox.
 *
 * Collects the photographs inside its own element, so the grid decides what
 * is viewable and this only handles showing it. Video tiles are left out: a
 * player is something you use where it sits, not something to open over the
 * page.
 *
 * Nothing here is load bearing. Every photograph is already a link to its own
 * file, so with JavaScript off, or before Alpine has started, clicking one
 * still opens it.
 */
Alpine.data('lightbox', () => ({
    isOpen: false,
    index: 0,
    items: [],
    trigger: null,

    init() {
        this.items = Array.from(this.$el.querySelectorAll('[data-lightbox-item]'));
    },

    get current() {
        return this.items[this.index] || null;
    },

    get source() {
        return this.current ? this.current.dataset.full : '';
    },

    get alt() {
        return this.current ? this.current.dataset.alt : '';
    },

    get caption() {
        return this.current ? this.current.dataset.caption : '';
    },

    get hasMany() {
        return this.items.length > 1;
    },

    show(element) {
        const index = this.items.indexOf(element);

        if (index === -1) {
            return;
        }

        this.trigger = element;
        this.index = index;
        this.isOpen = true;

        // The page behind must not scroll under the overlay.
        document.body.style.overflow = 'hidden';

        this.$nextTick(() => this.$refs.close && this.$refs.close.focus());
    },

    hide() {
        this.isOpen = false;
        document.body.style.overflow = '';

        // Back to the tile it came from, so the keyboard does not lose its
        // place in the grid.
        if (this.trigger) {
            this.trigger.focus();
            this.trigger = null;
        }
    },

    move(step) {
        if (! this.hasMany) {
            return;
        }

        this.index = (this.index + step + this.items.length) % this.items.length;
    },

    /**
     * Keeps Tab inside the overlay. Without this, tabbing would walk off into
     * the grid hidden behind it.
     */
    trapTab(event) {
        const order = [this.$refs.close, this.$refs.previous, this.$refs.next].filter(Boolean);

        if (order.length === 0) {
            return;
        }

        const at = order.indexOf(document.activeElement);
        const step = event.shiftKey ? -1 : 1;

        order[(at + step + order.length) % order.length].focus();
    },
}));
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

/**
 * Navigation progress.
 *
 * Shows a bar the moment a link that leads somewhere else is followed, and
 * again if the page is restored from the back/forward cache. Only same-tab,
 * same-origin navigations count: an anchor, a new tab, a download or a
 * mailto link is not a page load and should not pretend to be one.
 */
const setupNavigationProgress = () => {
    const bar = document.createElement('div');
    bar.className = 'nav-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);

    const start = () => bar.setAttribute('data-loading', '');
    const stop = () => bar.removeAttribute('data-loading');

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest('a');

        if (!link || link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        const href = link.getAttribute('href');

        if (!href || href.startsWith('#') || /^(mailto|tel|sms):/i.test(href)) {
            return;
        }

        const destination = new URL(link.href, window.location.href);

        if (destination.origin !== window.location.origin) {
            return;
        }

        // Same page, different anchor: nothing is loading.
        if (
            destination.pathname === window.location.pathname &&
            destination.search === window.location.search &&
            destination.hash
        ) {
            return;
        }

        start();
    });

    window.addEventListener('beforeunload', start);

    // Coming back through history serves a cached page, so the bar has to be
    // cleared or it would still be sitting there.
    window.addEventListener('pageshow', stop);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupNavigationProgress);
} else {
    setupNavigationProgress();
}
