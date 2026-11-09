// Connects the Slab renderer to the page: which floors exist, when they redraw,
// the pointer lamp, and the static floors on project cards.
(() => {
	const { gsap, TD, TDSlab: Slab } = window;
	const { $, $$, calm, hasHover } = TD;

	const entries = new Map();

	function makeSlab(canvas, options) {
		let slab;
		try {
			slab = new Slab(canvas, options);
		} catch (err) {
			document.documentElement.classList.add('no-webgl');
			console.warn('Tessaro floor:', err.message);
			return null;
		}

		const entry = { slab, canvas, visible: false, target: { x: 0.5, y: 0.5, a: 0 } };
		entries.set(canvas, entry);

		// Phone address bars change the height constantly, so small changes don't re-bake.
		let width = 0;
		let height = 0;
		const fit = () => {
			const w = canvas.clientWidth;
			const h = canvas.clientHeight;
			if (w !== width || Math.abs(h - height) > 120) {
				width = w;
				height = h;
				slab.resize(w, h);
			}
		};
		fit();
		new ResizeObserver(fit).observe(canvas);

		new IntersectionObserver(([item]) => {
			entry.visible = item.isIntersecting;
			if (entry.visible) { slab.dirty = true; }
		}, { rootMargin: '10% 0px' }).observe(canvas);

		if (options.live && hasHover) {
			const host = canvas.parentElement;
			host.addEventListener('pointermove', (e) => {
				const r = canvas.getBoundingClientRect();
				entry.target.x = (e.clientX - r.left) / r.width;
				entry.target.y = 1 - (e.clientY - r.top) / r.height;
				entry.target.a = 1;
			});
			host.addEventListener('pointerleave', () => { entry.target.a = 0; });
		}

		return entry;
	}

	// Only floors that are on screen and have changed get drawn.
	gsap.ticker.add(() => {
		entries.forEach(({ slab, visible, target }) => {
			if (!visible) { return; }
			const u = slab.u;
			if (Math.abs(u.px - target.x) + Math.abs(u.py - target.y) + Math.abs(u.pa - target.a) > 0.001) {
				u.px += (target.x - u.px) * 0.09;
				u.py += (target.y - u.py) * 0.09;
				u.pa += (target.a - u.pa) * 0.07;
				slab.dirty = true;
			}
			if (slab.dirty) { slab.render(); }
		});
	});

	function initFloors() {
		$$('[data-slab]').forEach((canvas) => {
			makeSlab(canvas, JSON.parse(canvas.dataset.slab));
		});
	}

	// Browsers limit WebGL contexts, so one shared context draws every card and
	// each result is copied to a plain 2D canvas.
	let baker = null;
	function bakeCard(canvas, mix) {
		try {
			if (!baker) {
				baker = new Slab(document.createElement('canvas'), { maxDpr: 1.5 });
			}
			baker.w = baker.h = 0;
			Object.assign(baker.o, { grid: 0 }, mix);
			baker.resize(canvas.clientWidth, canvas.clientHeight);
			baker.render();
			canvas.width = baker.W;
			canvas.height = baker.H;
			canvas.getContext('2d').drawImage(baker.canvas, 0, 0);
		} catch (err) {
			document.documentElement.classList.add('no-webgl');
		}
	}

	function initCards() {
		const cards = $$('[data-bake]');
		if (!cards.length) { return; }

		const observer = new IntersectionObserver((items) => {
			items.filter((item) => item.isIntersecting).forEach(({ target }) => {
				observer.unobserve(target);
				bakeCard(target, JSON.parse(target.dataset.bake));
				target.classList.add('is-baked');
			});
		}, { rootMargin: '60% 0px' });
		cards.forEach((card) => observer.observe(card));

		if (calm) { return; }
		$$('.card__floor canvas, .card__photo').forEach((el) => {
			gsap.fromTo(el, { yPercent: -5 }, {
				yPercent: 5,
				ease: 'none',
				scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true }
			});
		});
	}

	// The large floor at the top of a project page.
	function initFloorHero() {
		const hero = $('[data-floor-hero]');
		const entry = hero && entries.get($('canvas', hero));
		if (!entry || calm) { return; }

		const { slab } = entry;
		const redraw = () => { slab.dirty = true; };
		slab.u.reveal = 0;
		gsap.to(slab.u, { reveal: 1.6, duration: 2, ease: 'power2.inOut', onUpdate: redraw });
		gsap.to(slab.u, {
			scroll: 0.5,
			ease: 'none',
			scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true },
			onUpdate: redraw
		});
	}

	Object.assign(TD, {
		makeSlab,
		initFloors,
		initCards,
		initFloorHero,
		slabFor: (canvas) => entries.get(canvas)
	});
})();
