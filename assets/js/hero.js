// The opening floor, the stickers, and the statement section below it.
(() => {
	const { gsap, TD } = window;
	const { $, $$, calm, hasHover } = TD;

	function runBrush(slab) {
		const radius = () => Math.max(90, slab.h * 0.23);

		// Three passes that start and end past the screen edges. In 0 to 1 coordinates, y up.
		const points = [
			[-0.2, 0.74], [1.2, 0.74],
			[1.2, 0.5], [-0.2, 0.5],
			[-0.2, 0.26], [1.2, 0.26]
		];
		const lengths = [0];
		points.slice(1).forEach((p, i) => {
			lengths.push(lengths[i] + Math.hypot((p[0] - points[i][0]) * slab.w, (p[1] - points[i][1]) * slab.h));
		});

		const path = { distance: 0 };
		let last = points[0];
		let corner = 1;

		gsap.to(path, {
			distance: lengths[lengths.length - 1],
			duration: 3.2,
			delay: 0.25,
			ease: 'none',
			onUpdate() {
				// A slow device can pass a corner between frames. Stop at each one so
				// the stroke follows the path instead of cutting across the floor.
				while (corner < points.length - 1 && lengths[corner] <= path.distance) {
					slab.paint(last[0], last[1], points[corner][0], points[corner][1], radius());
					last = points[corner];
					corner++;
				}
				const a = points[corner - 1];
				const b = points[corner];
				const f = Math.min(1, (path.distance - lengths[corner - 1]) / (lengths[corner] - lengths[corner - 1]));
				const x = a[0] + (b[0] - a[0]) * f;
				const y = a[1] + (b[1] - a[1]) * f;
				slab.paint(last[0], last[1], x, y, radius());
				last = [x, y];
			},
			onComplete() {
				// Clear anything left in the far corners.
				gsap.to(slab.u, {
					reveal: 2,
					duration: 0.9,
					ease: 'power1.out',
					onUpdate() { slab.dirty = true; }
				});
			}
		});
	}

	function initHero() {
		const hero = $('[data-hero]');
		if (!hero) { return; }

		// The ticker sits on top of the foot bar, whose height depends on its text.
		const foot = $('.hero__foot', hero);
		const syncFoot = () => hero.style.setProperty('--foot-h', foot.offsetHeight + 'px');
		syncFoot();
		new ResizeObserver(syncFoot).observe(foot);

		initStickers(hero);
		gsap.to($('[data-hero-hint]', hero), { autoAlpha: 1, duration: 0.6, delay: calm ? 0 : 3.6 });

		const entry = TD.slabFor($('canvas', hero));
		if (!entry) { return; }

		if (calm) {
			entry.slab.u.reveal = 2;
			entry.slab.dirty = true;
			return;
		}
		entry.slab.u.reveal = 0;
		runBrush(entry.slab);
	}

	function initStickers(scope) {
		$$('[data-sticker]', scope).forEach((el) => {
			const face = $('.sticker__face', el);
			const tilt = parseFloat(el.dataset.tilt) || 0;
			const onScroll = el.hasAttribute('data-on-scroll');

			if (calm) {
				gsap.set(face, { rotation: tilt, autoAlpha: 1 });
				return;
			}

			// Hero stickers land on a timer as the brush passes; others when scrolled to.
			gsap.fromTo(face, { scale: 1.7, rotation: tilt - 22, autoAlpha: 0 }, {
				scale: 1,
				rotation: tilt,
				autoAlpha: 1,
				duration: 0.55,
				delay: onScroll ? 0 : parseFloat(el.dataset.at) || 1,
				ease: 'back.out(2)',
				scrollTrigger: onScroll ? { trigger: el, start: 'top 84%', once: true } : null,
				onComplete() {
					gsap.to(face, {
						y: gsap.utils.random(-7, 7),
						rotation: tilt + gsap.utils.random(-1.6, 1.6),
						duration: gsap.utils.random(2.2, 3.4),
						ease: 'sine.inOut',
						yoyo: true,
						repeat: -1
					});
				}
			});

			const speed = parseFloat(el.dataset.speed) || 100;
			gsap.fromTo(el, { y: onScroll ? speed / 2 : 0 }, {
				y: onScroll ? -speed / 2 : -speed,
				ease: 'none',
				scrollTrigger: { trigger: scope, start: onScroll ? 'top bottom' : 'top top', end: 'bottom top', scrub: true }
			});

			if (hasHover) { makeDraggable($('.sticker__drag', el), scope); }
		});
	}

	function makeDraggable(el, area) {
		let held = null;
		let moved = false;

		const onMove = (e) => {
			if (!held) { return; }
			const now = performance.now();
			const elapsed = Math.max(1, now - held.lastTime);
			held.speedX = (e.clientX - held.lastX) / elapsed;
			held.speedY = (e.clientY - held.lastY) / elapsed;
			held.lastX = e.clientX;
			held.lastY = e.clientY;
			held.lastTime = now;

			const dx = e.clientX - held.startX;
			const dy = e.clientY - held.startY;
			if (Math.abs(dx) + Math.abs(dy) > 5) { moved = true; }
			if (moved) { gsap.set(el, { x: held.x + dx, y: held.y + dy }); }
		};

		const release = () => {
			window.removeEventListener('pointermove', onMove);
			window.removeEventListener('pointerup', release);
			window.removeEventListener('pointercancel', release);
			if (!held) { return; }

			// Slide a little in the direction it was thrown, staying inside the section
			// and above the ticker. No slide if the pointer had already stopped.
			const r = el.getBoundingClientRect();
			const box = area.getBoundingClientRect();
			const ticker = $('.ticker', area);
			const bottom = ticker ? ticker.getBoundingClientRect().top : box.bottom;
			const stillMoving = moved && performance.now() - held.lastTime < 80;
			const cap = gsap.utils.clamp(-1.6, 1.6);
			const slideX = stillMoving ? cap(held.speedX) * 150 : 0;
			const slideY = stillMoving ? cap(held.speedY) * 150 : 0;

			gsap.to(el, {
				x: gsap.getProperty(el, 'x') + (moved ? Math.max(box.left - r.left, Math.min(box.right - r.right, slideX)) : 0),
				y: gsap.getProperty(el, 'y') + (moved ? Math.max(box.top - r.top, Math.min(bottom - r.bottom, slideY)) : 0),
				scale: 1,
				duration: 0.65,
				ease: 'power3.out'
			});
			el.classList.remove('is-held');
			held = null;
		};

		el.addEventListener('dragstart', (e) => e.preventDefault());

		// Stops text being selected during a drag. Clicks on the link sticker still fire.
		el.addEventListener('mousedown', (e) => e.preventDefault());

		el.addEventListener('pointerdown', (e) => {
			if (e.button !== 0) { return; }
			gsap.killTweensOf(el);
			held = {
				startX: e.clientX,
				startY: e.clientY,
				x: gsap.getProperty(el, 'x'),
				y: gsap.getProperty(el, 'y'),
				lastX: e.clientX,
				lastY: e.clientY,
				lastTime: performance.now(),
				speedX: 0,
				speedY: 0
			};
			moved = false;
			el.classList.add('is-held');
			gsap.to(el, { scale: 1.08, duration: 0.2, ease: 'power2.out' });

			// Listen on the window. A fast flick can leave the sticker before its first move
			// event, and pointer capture would retarget the click and break the link sticker.
			window.addEventListener('pointermove', onMove);
			window.addEventListener('pointerup', release);
			window.addEventListener('pointercancel', release);
		});

		// Dropping the link sticker after a drag shouldn't also follow the link.
		el.addEventListener('click', (e) => {
			if (moved) {
				e.preventDefault();
				e.stopPropagation();
				moved = false;
			}
		}, true);
	}

	function initSay() {
		const say = $('[data-say]');
		if (!say) { return; }

		initStickers(say);
		if (calm) { return; }

		$$('[data-chip]', say).forEach((chip) => {
			const drift = parseFloat(chip.dataset.chip) || 100;
			gsap.fromTo(chip, { y: drift * 0.5, rotation: -drift * 0.1 }, {
				y: -drift * 0.5,
				rotation: drift * 0.1,
				ease: 'none',
				scrollTrigger: { trigger: say, start: 'top bottom', end: 'bottom top', scrub: true }
			});
		});
	}

	Object.assign(TD, { initHero, initSay });
})();
