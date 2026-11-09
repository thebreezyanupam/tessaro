// Sends the enquiry form over REST. Without JavaScript it posts to admin-post.php instead.
(() => {
	const { $ } = window.TD;

	const form = $('[data-enquiry]');
	if (!form) { return; }

	const status = $('[data-form-status]', form);
	const button = $('button[type="submit"]', form);
	const FALLBACK = 'That did not go through. Please call us instead.';

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		button.disabled = true;
		status.textContent = 'Sending...';

		try {
			const response = await fetch(form.dataset.endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(Object.fromEntries(new FormData(form)))
			});
			const data = await response.json();

			if (response.ok) {
				form.reset();
				$('[data-form-mix-card]', form).hidden = true;
				$('[data-form-mix]', form).value = '';
				status.textContent = 'Thanks, we have it. We will call you within two working days.';
			} else {
				status.textContent = data.message || FALLBACK;
			}
		} catch (err) {
			status.textContent = FALLBACK;
		} finally {
			button.disabled = false;
		}
	});
})();
