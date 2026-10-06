(() => {
	'use strict';
	const cfg = window.TAP_WORKER || {};
	if (!cfg.restUrl || !cfg.sessionKey) return;
	const beat = async () => {
		try {
			await fetch(cfg.restUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce || '' },
				body: JSON.stringify({ session_key: cfg.sessionKey })
			});
		} catch (e) {}
	};
	beat();
	window.setInterval(beat, 30000);
})();
