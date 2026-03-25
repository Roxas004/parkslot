const search = document.getElementById('search');
const filter = document.getElementById('filter');
const rows = document.querySelectorAll('#table-body tr');

function filterTable() {
    const term = search.value.toLowerCase();
    const statusFilter = filter.value;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.dataset.status;

        const matchesSearch = text.includes(term);
        const matchesFilter = !statusFilter || status === statusFilter;

        row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}

search.addEventListener('input', filterTable);
filter.addEventListener('change', filterTable);
