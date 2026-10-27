/* ============================================================
   MERINAZ frontend.js — lightbox, back-to-top, toasts
   ============================================================ */
(function () {
    'use strict';

    /* ---------------- Toast helper ---------------- */
    window.merinazToast = function (message) {
        var toastEl = document.getElementById('liveToast');
        var msgEl = document.getElementById('toastMsg');

        if (!toastEl || !msgEl || typeof bootstrap === 'undefined') {
            return;
        }

        msgEl.textContent = message;
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2600 }).show();
    };

    /* ---------------- Gallery lightbox ---------------- */
    var lightboxItems = [];
    var lightboxIndex = 0;

    function collectLightboxItems() {
        lightboxItems = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox-src]'));
    }

    function renderLightbox() {
        var item = lightboxItems[lightboxIndex];

        if (!item) {
            return;
        }

        var img = document.getElementById('lightboxImg');
        var caption = document.getElementById('lightboxCap');

        if (img) {
            img.src = item.getAttribute('data-lightbox-src');
            img.alt = item.getAttribute('data-lightbox-alt') || 'Gallery image';
        }

        if (caption) {
            var text = item.getAttribute('data-lightbox-caption') || '';
            caption.textContent = (lightboxIndex + 1) + ' / ' + lightboxItems.length + (text ? ' — ' + text : '');
        }
    }

    function openLightbox(index) {
        collectLightboxItems();
        lightboxIndex = index;

        renderLightbox();

        var modalEl = document.getElementById('lightboxModal');

        if (modalEl && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    function stepLightbox(step) {
        if (!lightboxItems.length) {
            return;
        }

        lightboxIndex = (lightboxIndex + step + lightboxItems.length) % lightboxItems.length;
        renderLightbox();
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-lightbox-index]');

        if (trigger) {
            event.preventDefault();
            openLightbox(parseInt(trigger.getAttribute('data-lightbox-index'), 10) || 0);
        }
    });

    document.addEventListener('keydown', function (event) {
        var modalEl = document.getElementById('lightboxModal');

        if (modalEl && modalEl.classList.contains('show')) {
            if (event.key === 'ArrowRight') {
                stepLightbox(1);
            }

            if (event.key === 'ArrowLeft') {
                stepLightbox(-1);
            }
        }
    });

    /* ---------------- Back to top ---------------- */
    var backTop = document.getElementById('backTop');

    if (backTop) {
        window.addEventListener('scroll', function () {
            backTop.style.display = window.scrollY > 600 ? 'block' : 'none';
        });

        backTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
