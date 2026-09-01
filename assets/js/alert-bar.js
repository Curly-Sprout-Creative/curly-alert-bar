/**
 * Curly Alert Bar — front-end visibility.
 *
 * Hides the .alert-bar Oxygen element when the admin has disabled it, when the
 * text is empty (server-side flag), or when the visitor dismissed it this
 * session. Attaches the close button handler otherwise.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var alertBar = document.querySelector('.alert-bar');
		if (!alertBar) return;

		var cfg = window.curlyAlertBar || {};
		var isServerDisabled = cfg.serverDisabled === true;
		var isSessionClosed = sessionStorage.getItem(cfg.closeKey || 'alertBarClosed') === 'true';

		if (isServerDisabled || isSessionClosed) {
			alertBar.style.display = 'none';
			return;
		}

		var closeBtn = alertBar.querySelector('.alert-bar-close');
		if (closeBtn) {
			closeBtn.style.cursor = 'pointer';
			closeBtn.addEventListener('click', function () {
				alertBar.style.display = 'none';
				sessionStorage.setItem(cfg.closeKey || 'alertBarClosed', 'true');
			});
		}
	});
})();