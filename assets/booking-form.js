(function () {
    'use strict';

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('.jankx-travel-booking-form');
        if (!form) {
            return;
        }
        event.preventDefault();

        var config = window.jankxTravelBooking;
        if (!config) {
            return;
        }

        var statusEl = form.querySelector('.jankx-travel-booking-status');
        var submitBtn = form.querySelector('[type="submit"]');
        var formData = new FormData(form);
        formData.append('action', config.action);
        formData.append('nonce', config.nonce);

        if (submitBtn) {
            submitBtn.disabled = true;
        }
        if (statusEl) {
            statusEl.textContent = config.i18n.sending;
            statusEl.className = 'jankx-travel-booking-status is-sending';
        }

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (result && result.success) {
                    if (statusEl) {
                        statusEl.textContent = result.data.message || config.i18n.success;
                        statusEl.className = 'jankx-travel-booking-status is-success';
                    }
                    form.reset();
                } else {
                    if (statusEl) {
                        statusEl.textContent = (result && result.data && result.data.message) || config.i18n.error;
                        statusEl.className = 'jankx-travel-booking-status is-error';
                    }
                }
            })
            .catch(function () {
                if (statusEl) {
                    statusEl.textContent = config.i18n.error;
                    statusEl.className = 'jankx-travel-booking-status is-error';
                }
            })
            .finally(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            });
    });
})();
