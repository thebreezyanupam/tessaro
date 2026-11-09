// Shared helpers and site-wide behaviour: smooth scrolling, in-page links, the header.
// The other scripts read what they need from window.TD.
window.TD = (() => {
	const { gsap, ScrollTrigger, SplitText } = window;

	const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const hasHover = matchMedia('(hover: hover)').matches;
	const $ = (selector, scope = document) => scope.querySelector(selector);
	const $$ = (selector, scope = document) => [...scope.querySelectorAll(selector)];

	document.documentElement.classList.add('js');
	gsap.registerPlugin(ScrollTrigger, SplitText);

	let lenis = null;
	if (!calm) {
		lenis = new window.Lenis({ lerp: 0.1 });
		lenis.on('scroll', ScrollTrigger.update);
		gsap.ticker.add((time) => lenis.raf(time * 1000));
		gsap.ticker.lagSmoothing(0);
	}

	const bar = $('[data-bar]');
	const toggle = $('[data-nav-toggle]');
	const nav = $('[data-nav]');

	function closeNav() {
		toggle.setAttribute('aria-expanded', 'false');
		nav.classList.remove('is-open');
		if (lenis) { lenis.start(); }
	}

	toggle.addEventListener('click', () => {
		const open = toggle.getAttribute('aria-expanded') !== 'true';
		toggle.setAttribute('aria-expanded', String(open));
		nav.classList.toggle('is-open', open);
		if (lenis) { lenis[open ? 'stop' : 'start'](); }
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') { closeNav(); }
	});

	// Hide the header when scrolling down, bring it back when scrolling up.
	let lastY = 0;
	ScrollTrigger.create({
		start: 0,
		end: 'max',
		onUpdate(self) {
			const y = self.scroll();
			bar.classList.toggle('is-tucked', y > lastY && y > 300);
			lastY = y;
		}
	});

	document.addEventListener('click', (e) => {
		const link = e.target.closest('a[href*="#"]');
		if (!link || link.pathname !== location.pathname || link.host !== location.host || !link.hash) {
			return;
		}
		const target = document.getElementById(link.hash.slice(1));
		if (!target) { return; }

		e.preventDefault();
		closeNav();
		if (lenis) {
			lenis.scrollTo(target, { duration: 1.3 });
		} else {
			target.scrollIntoView();
		}
		history.replaceState(null, '', link.hash);
	});

	// Pinned sections add height after load, so the browser's own jump to a #hash lands in
	// the wrong place. Run this once everything is set up.
	function settleScroll() {
		const target = location.hash ? document.getElementById(location.hash.slice(1)) : null;
		if (target) {
			if (lenis) {
				lenis.scrollTo(target, { immediate: true, force: true });
			} else {
				target.scrollIntoView();
			}
			return;
		}
		const navigation = performance.getEntriesByType('navigation')[0];
		if (navigation && navigation.type === 'navigate') {
			scrollTo(0, 0);
		}
	}

	return { calm, hasHover, $, $$, settleScroll };
})();
