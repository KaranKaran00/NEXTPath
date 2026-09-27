/**
 * NEXTPath - shared UI interactions
 * Mobile nav toggle, sidebar toggle, password show/hide,
 * and confirmation dialogs for destructive admin actions.
 */

document.addEventListener('DOMContentLoaded', function () {

    /* Public navbar mobile toggle */
    var navToggle = document.getElementById('navToggle');
    var siteNav = document.querySelector('.site-nav');
    if (navToggle && siteNav) {
        navToggle.addEventListener('click', function () {
            var isOpen = siteNav.classList.toggle('nav-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    /* Dashboard sidebar toggle (mobile) */
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (sidebar.classList.contains('open')
                && !sidebar.contains(e.target)
                && e.target !== sidebarToggle) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* Password show/hide */
    document.querySelectorAll('.password-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.textContent = isHidden ? 'Hide' : 'Show';
        });
    });

    /* Confirmation dialogs before destructive admin actions */
    document.querySelectorAll('.confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var message = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    /* Auto-dismiss flash messages after a few seconds */
    document.querySelectorAll('.flash').forEach(function (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 300ms ease';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 300);
        }, 4000);
    });

});
