// Mix Your Own: visitors build a floor recipe and can attach it to their enquiry.
(() => {
	const { TD } = window;
	const { $, $$ } = TD;

	const MAX_STONES = 6;
	const sameColour = (a, b) => a.toUpperCase() === b.toUpperCase();

	// Project pages link here with ?mix=binder|stone.stone|size|density|seed
	function readLinkedMix() {
		const mix = new URLSearchParams(location.search).get('mix');
		if (!mix) { return null; }

		const [binder, stones = '', size, density, seed] = mix.split('|');
		const isHex = (value) => /^[0-9a-f]{6}$/i.test(value);
		const validStones = stones.split('.').filter(isHex);
		if (!isHex(binder) || !validStones.length) { return null; }

		return {
			binder: '#' + binder,
			stones: validStones.map((hex) => '#' + hex).slice(0, MAX_STONES),
			size: Math.min(130, Math.max(20, parseFloat(size) || 62)),
			density: Math.min(1.3, Math.max(0.45, parseFloat(density) || 0.95)),
			seed: parseInt(seed, 10) || 12
		};
	}

	// A short code the yard can quote back. The same recipe always gets the same code.
	function mixCode(state) {
		const text = JSON.stringify(state);
		let hash = 0;
		for (let i = 0; i < text.length; i++) {
			hash = (hash * 31 + text.charCodeAt(i)) >>> 0;
		}
		return 'TD-' + hash.toString(36).toUpperCase().slice(0, 5);
	}

	function attachToForm(state, code, entry) {
		const card = $('[data-form-mix-card]');
		$('[data-form-mix]').value = code + ' ' + JSON.stringify(state);
		$('[data-form-mix-code]').textContent = code;
		card.hidden = false;

		if (entry) {
			// WebGL clears its canvas after each frame, so draw first and copy straight away.
			entry.slab.render();
			const preview = $('[data-form-mix-preview]');
			const source = entry.slab.canvas;
			const side = Math.min(source.width, source.height);
			preview.getContext('2d').drawImage(
				source,
				(source.width - side) / 2,
				(source.height - side) / 2,
				side,
				side,
				0,
				0,
				preview.width,
				preview.height
			);
		}
	}

	TD.initMix = () => {
		const section = $('[data-mix]');
		if (!section) { return; }

		const binders = $$('[data-binder]', section);
		const chips = $$('[data-stone]', section);
		const size = $('[data-mix-size]', section);
		const density = $('[data-mix-density]', section);
		const codeLabel = $('[data-mix-code]', section);

		const state = readLinkedMix() || {
			binder: '#EAE2D0',
			stones: chips.filter((chip) => chip.getAttribute('aria-pressed') === 'true').map((chip) => chip.dataset.stone),
			size: parseFloat(size.value),
			density: parseFloat(density.value),
			seed: 12
		};
		size.value = state.size;
		density.value = state.density;

		const entry = TD.makeSlab($('[data-mix-slab]', section), { live: true, maxDpr: 1.5, ...state });
		let code = '';

		const update = () => {
			binders.forEach((button) => {
				button.setAttribute('aria-pressed', String(sameColour(button.dataset.binder, state.binder)));
			});
			chips.forEach((chip) => {
				const on = state.stones.some((stone) => sameColour(stone, chip.dataset.stone));
				chip.setAttribute('aria-pressed', String(on));
			});
			code = mixCode(state);
			codeLabel.textContent = code;
			if (entry) { entry.slab.setMix(state); }
		};

		binders.forEach((button) => {
			button.addEventListener('click', () => {
				state.binder = button.dataset.binder;
				update();
			});
		});

		chips.forEach((chip) => {
			chip.addEventListener('click', () => {
				const hex = chip.dataset.stone;
				const index = state.stones.findIndex((stone) => sameColour(stone, hex));
				if (index >= 0) {
					if (state.stones.length > 1) { state.stones.splice(index, 1); }
				} else {
					state.stones.push(hex);
					if (state.stones.length > MAX_STONES) { state.stones.shift(); }
				}
				update();
			});
		});

		size.addEventListener('input', () => {
			state.size = parseFloat(size.value);
			update();
		});
		density.addEventListener('input', () => {
			state.density = parseFloat(density.value);
			update();
		});

		$('[data-mix-shuffle]', section).addEventListener('click', () => {
			state.seed = Math.floor(Math.random() * 9000) + 1;
			update();
		});

		$('[data-mix-send]', section).addEventListener('click', () => attachToForm(state, code, entry));

		update();
	};
})();
