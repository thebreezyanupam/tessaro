// Starts everything once the display font is ready, because the hero floor has it inlaid.
(() => {
	const { ScrollTrigger, TD } = window;

	document.fonts.load('900 100px "Anybody"').catch(() => {}).then(() => {
		TD.initFloors();
		TD.initHero();
		TD.initSay();
		TD.initProcess();
		TD.initMix();
		TD.initCards();
		TD.initType();
		TD.initFloorHero();

		ScrollTrigger.refresh();
		document.documentElement.classList.add('is-ready');
		TD.settleScroll();
	});
})();
