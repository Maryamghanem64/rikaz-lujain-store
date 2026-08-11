import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.querySelectorAll('[x-show]').forEach((element) => {
    if (![...element.attributes].some((attribute) => attribute.name.startsWith('x-transition'))) {
        element.setAttribute('x-transition.opacity.duration.200ms', '');
    }
});

Alpine.start();

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

const prepareMotion = () => {
    const revealGroups = [
        ['.editorial-hero .eyebrow, .editorial-hero h1, .editorial-hero h1 + p, .editorial-hero .flex.flex-wrap, .editorial-hero .hero-media', 85],
        ['.brand-signature, .category-tile, .product-card', 75],
        ['.editorial-break figure, .editorial-break .eyebrow, .editorial-break h2, .editorial-break h2 + p', 80],
        ['.rikaz-brand-intro > div > *, .rikaz-brand-main, .rikaz-brand-detail, .lujain-brand-gallery figure, .store-section-heading, .catalog-filters', 70],
        ['.store-body main article, .store-body main aside, .store-body main form > section, .store-body main .alert-success, .store-body main .alert-error, .store-body main > section > .container-shell > .mx-auto', 65],
        ['.admin-content > * > *, .admin-card, .admin-content .alert-success, .admin-content .alert-error, body > main .surface-card', 45],
        ['footer .container-shell > *', 55],
    ];

    revealGroups.forEach(([selector, step]) => {
        document.querySelectorAll(selector).forEach((element, index) => {
            if (element.dataset.reveal !== undefined) return;
            element.dataset.reveal = element.matches('.brand-logo-circle, .hero-media, figure') ? 'scale' : 'fade-up';
            element.style.setProperty('--reveal-delay', `${Math.min(index * step, 320)}ms`);
        });
    });

    document.querySelectorAll('.brand-logo-circle').forEach((logo, index) => {
        logo.dataset.reveal = 'scale';
        logo.style.setProperty('--reveal-delay', `${index * 80}ms`);
    });

    const revealItems = [...document.querySelectorAll('[data-reveal]')];
    document.documentElement.classList.add('motion-ready');

    if (reducedMotion.matches || !('IntersectionObserver' in window)) {
        revealItems.forEach((element) => element.classList.add('is-revealed'));
    } else {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -7% 0px', threshold: 0.08 });
        revealItems.forEach((element) => observer.observe(element));
    }

    const galleryImage = document.querySelector('[data-gallery-main]');
    if (galleryImage) {
        new MutationObserver(() => {
            if (reducedMotion.matches) return;
            galleryImage.classList.remove('gallery-image-change');
            requestAnimationFrame(() => galleryImage.classList.add('gallery-image-change'));
        }).observe(galleryImage, { attributes: true, attributeFilter: ['src'] });
    }

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            form.setAttribute('aria-busy', 'true');
            form.querySelectorAll('button[type="submit"]').forEach((button) => button.classList.add('is-submitting'));
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', prepareMotion, { once: true });
} else {
    prepareMotion();
}
