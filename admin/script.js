(function () {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('consultationsTable') || document.getElementById('contactsTable');
    const noResults = document.getElementById('noResults');

    if (!searchInput || !table) return;

    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(
        row => !row.querySelector('.text-center') // skip the "Aucune consultation / Aucun message" row
    );

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matches = query === '' || text.includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        if (noResults) {
            noResults.style.display = (query !== '' && visibleCount === 0) ? 'block' : 'none';
        }
    });
})();
