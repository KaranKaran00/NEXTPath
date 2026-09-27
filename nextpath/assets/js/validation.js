/**
 * NEXTPath - Client-side form validation
 * Mirrors the server-side checks in includes/functions.php so users get
 * instant feedback, but the PHP validation is always the source of truth.
 */

(function () {

    function showError(input, message) {
        input.classList.toggle('invalid', Boolean(message));
        var group = input.closest('.form-group');
        if (!group) return;
        var errorEl = group.querySelector('.field-error');
        if (errorEl) errorEl.textContent = message || '';
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function isValidMobile(value) {
        return /^[0-9]{10}$/.test(value);
    }

    function validateField(input) {
        var value = input.value.trim();

        if (input.hasAttribute('required') && value === '') {
            showError(input, 'This field is required.');
            return false;
        }

        if (input.type === 'email' && value !== '' && !isValidEmail(value)) {
            showError(input, 'Please enter a valid email address.');
            return false;
        }

        if (input.id === 'mobile' && value !== '' && !isValidMobile(value)) {
            showError(input, 'Please enter a valid 10-digit mobile number.');
            return false;
        }

        if (input.id === 'password' && value !== '' && value.length < 6) {
            showError(input, 'Password must be at least 6 characters.');
            return false;
        }

        showError(input, '');
        return true;
    }

    function attachLiveValidation(form) {
        var fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(function (field) {
            field.addEventListener('blur', function () { validateField(field); });
        });

        form.addEventListener('submit', function (e) {
            var valid = true;
            fields.forEach(function (field) {
                if (!validateField(field)) valid = false;
            });
            if (!valid) {
                e.preventDefault();
                var firstInvalid = form.querySelector('.invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var loginForm = document.getElementById('loginForm');
        var registerForm = document.getElementById('registerForm');
        if (loginForm) attachLiveValidation(loginForm);
        if (registerForm) attachLiveValidation(registerForm);
    });
})();
