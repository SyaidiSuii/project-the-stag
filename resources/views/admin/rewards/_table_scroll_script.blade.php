<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make table scrollable if more than 6 rows
    const tableContainer = document.querySelector('.admin-table-container');
    const tableRows = document.querySelectorAll('.admin-table tbody tr');

    // Count actual data rows (exclude empty state)
    let dataRowCount = 0;
    tableRows.forEach(row => {
        if (!row.querySelector('td[colspan]')) {
            dataRowCount++;
        }
    });

    if (dataRowCount > 6) {
        tableContainer.classList.add('scrollable');
    }
});
</script>
