// Text animation: statement words fading in, stretching headings, rows rising into place.
(() => {
	const { gsap, ScrollTrigger, SplitText, TD } = window;
	const { $$, calm } = TD;

	// Size a heading so that at full width (wdth 150) it exactly fills its column.
	function fitHeading(el) {
		const previous = el.style.getPropertyValue('--wd');
		el.style.setProperty('--wd', '150');
		el.style.fontSize = '100px';

		const range = document.createRange();
		range.selectNodeContents(el);
		const textWidth = range.getBoundingClientRect().width;

		const parent = getComputedStyle(el.parentElement);
		const column = el.parentElement.clientWidth - parseFloat(parent.paddingLeft) - parseFloat(parent.paddingRight);
		const size = 100 * column * 0.99 / textWidth;
		el.style.fontSize = Math.min(size, innerHeight * 0.34) + 'px';

		if (previous) {
			el.style.setProperty('--wd', previous);
		} else {
			el.style.removeProperty('--wd');
		}
	}

	TD.initType = () => {
		if (calm) { return; }

		$$('[data-words]').forEach((el) => {
			const { words } = new SplitText(el, { type: 'words' });
			gsap.fromTo(words, { opacity: 0.12 }, {
				opacity: 1,
				stagger: 0.05,
				ease: 'none',
				scrollTrigger: { trigger: el, start: 'top 78%', end: 'bottom 55%', scrub: true }
			});
		});

		const headings = $$('[data-stretch]');
		headings.forEach(fitHeading);

		let width = innerWidth;
		addEventListener('resize', () => {
			if (innerWidth !== width) {
				width = innerWidth;
				headings.forEach(fitHeading);
				ScrollTrigger.refresh();
			}
		});

		headings.forEach((el) => {
			gsap.fromTo(el, { '--wd': 50 }, {
				'--wd': 150,
				ease: 'none',
				scrollTrigger: { trigger: el, start: 'top 96%', end: 'top 42%', scrub: true }
			});
		});

		$$('[data-rise]').forEach((el) => {
			gsap.from(el, {
				y: 36,
				opacity: 0,
				duration: 0.9,
				ease: 'power3.out',
				scrollTrigger: { trigger: el, start: 'top 90%', once: true }
			});
		});
	};
})();
