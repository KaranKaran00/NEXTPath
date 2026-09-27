/**
 * NEXTPath - Career Explorer
 * Client-side search/filter and accordion expand/collapse.
 * The dataset is small (28 categories) so this filters in the
 * browser rather than round-tripping to the server.
 */

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('explorerSearch');
    var cards = Array.prototype.slice.call(document.querySelectorAll('.explorer-card'));
    var noResults = document.getElementById('explorerNoResults');

    cards.forEach(function (card) {
        var head = card.querySelector('.explorer-card-head');
        head.addEventListener('click', function () {
            var isOpen = card.classList.contains('open');
            card.classList.toggle('open', !isOpen);
            head.setAttribute('aria-expanded', (!isOpen).toString());
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = searchInput.value.trim().toLowerCase();
            var visibleCount = 0;

            cards.forEach(function (card) {
                var haystack = card.getAttribute('data-search') || '';
                var matches = query === '' || haystack.indexOf(query) !== -1;
                card.hidden = !matches;
                if (matches) {
                    visibleCount += 1;
                    if (query !== '') {
                        card.classList.add('open');
                    }
                }
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    }
});
