/**
 * NEXTPath - Assessment navigation
 * Handles one-question-at-a-time flow, the progress bar,
 * and answer selection styling. Submission itself is a
 * normal form POST to submit_assessment.php.
 */

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('assessmentForm');
    if (!form) return;

    var cards = Array.prototype.slice.call(form.querySelectorAll('.question-card'));
    var total = cards.length;
    var current = 0;

    var counterEl = document.getElementById('questionCounter');
    var progressFill = document.getElementById('progressFill');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var submitBtn = document.getElementById('submitBtn');

    function pad(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function render() {
        cards.forEach(function (card, i) {
            card.classList.toggle('active', i === current);
        });

        counterEl.textContent = 'Question ' + pad(current + 1) + ' / ' + pad(total);
        progressFill.style.width = (((current + 1) / total) * 100) + '%';

        prevBtn.disabled = current === 0;

        var isLast = current === total - 1;
        nextBtn.style.display = isLast ? 'none' : 'inline-flex';
        submitBtn.style.display = isLast ? 'inline-flex' : 'none';
    }

    function isAnswered(index) {
        var inputs = cards[index].querySelectorAll('input[type="radio"]');
        return Array.prototype.some.call(inputs, function (input) { return input.checked; });
    }

    prevBtn.addEventListener('click', function () {
        if (current > 0) {
            current -= 1;
            render();
        }
    });

    nextBtn.addEventListener('click', function () {
        if (!isAnswered(current)) {
            cards[current].classList.add('shake');
            setTimeout(function () { cards[current].classList.remove('shake'); }, 300);
            return;
        }
        if (current < total - 1) {
            current += 1;
            render();
        }
    });

    // Visual feedback + auto-advance-friendly styling on selection.
    form.querySelectorAll('.option-item').forEach(function (option) {
        var input = option.querySelector('input[type="radio"]');
        input.addEventListener('change', function () {
            var siblings = option.closest('.options-list').querySelectorAll('.option-item');
            siblings.forEach(function (s) { s.classList.remove('selected'); });
            option.classList.add('selected');
        });
    });

    form.addEventListener('submit', function (e) {
        var unanswered = cards.findIndex(function (_, i) { return !isAnswered(i); });
        if (unanswered !== -1) {
            e.preventDefault();
            current = unanswered;
            render();
            window.alert('Please answer every question before submitting.');
        }
    });

    render();
});
