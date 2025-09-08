document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchInput');
  const tableBody = document.querySelector('.admin-table tbody');

  // 🔍 Search filter (client-side sahaja)
  searchInput.addEventListener('keyup', () => {
    const searchTerm = searchInput.value.toLowerCase();
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });

  // 🌙 Animation contoh (fade in table rows)
  tableBody.querySelectorAll('tr').forEach((row, i) => {
    row.style.opacity = 0;
    setTimeout(() => {
      row.style.transition = 'opacity 0.5s ease';
      row.style.opacity = 1;
    }, i * 150); // delay sikit untuk effect "staggered fade-in"
  });
});