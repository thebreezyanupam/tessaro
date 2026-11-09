// "How a floor gets made": one pinned floor goes through all four stages as you scroll.
(() => {
	const { ScrollTrigger, TD } = window;
	const { $, $$, calm } = TD;

	const smoothstep = (from, to, x) => {
		const t = Math.max(0, Math.min(1, (x - from) / (to - from)));
		return t * t * (3 - 2 * t);
	};

	TD.initProcess = () => {
		const section = $('[data-process]');
		if (!section) { return; }

		const entry = TD.slabFor($('canvas', section));
		if (calm || !entry) {
			section.classList.add('is-stacked');
			return;
		}
		section.classList.add('is-pinned');

		const { slab } = entry;
		const u = slab.u;
		const steps = $$('[data-step]', section);
		const bar = $('[data-process-bar]', section);

		let current = -1;
		const showStep = (index) => {
			if (index === current) { return; }
			current = index;
			steps.forEach((step, i) => step.classList.toggle('is-on', i === index));
		};

		showStep(0);
		Object.assign(u, { reveal: 0, polish: 0, wet: 1, chips: 1 });

		ScrollTrigger.create({
			trigger: section,
			start: 'top top',
			end: '+=340%',
			pin: true,
			scrub: 0.4,
			onUpdate({ progress }) {
				u.wet = 1 - smoothstep(0.17, 0.32, progress);
				u.chips = 1 - 0.62 * smoothstep(0.17, 0.32, progress);
				u.reveal = smoothstep(0.5, 0.74, progress) * 1.15;
				u.polish = smoothstep(0.74, 0.94, progress);
				u.scroll = progress * 1.1;
				slab.dirty = true;
				showStep(Math.min(3, Math.floor(progress * 4)));
				bar.style.transform = `scaleX(${progress})`;
			}
		});
	};
})();
