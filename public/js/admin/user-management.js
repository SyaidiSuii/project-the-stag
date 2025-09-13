document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');
    
    // 🌙 Animation contoh (fade in table rows) - with safety check
    const tableBody = document.querySelector('tbody');
    if (tableBody) {
        tableBody.querySelectorAll('tr').forEach((row, i) => {
            row.style.opacity = 0;
            setTimeout(() => {
                row.style.transition = 'opacity 0.5s ease';
                row.style.opacity = 1;
            }, i * 150); // delay sikit untuk effect "staggered fade-in"
        });
    }
    
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    
    // Debug - check if elements are found
    console.log('searchInput:', searchInput);
    console.log('roleFilter:', roleFilter);
    console.log('statusFilter:', statusFilter);
    console.log('sortBy:', sortBy);
    
    // Function to apply filters
    function applyFilters() {
        console.log('Applying filters...');
        const url = new URL(window.location.href);
        
        // Get current values
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const roleValue = roleFilter ? roleFilter.value : 'all';
        const statusValue = statusFilter ? statusFilter.value : 'all';
        const sortValue = sortBy ? sortBy.value : 'newest';
        
        // Clear existing params
        url.searchParams.delete('search');
        url.searchParams.delete('role');
        url.searchParams.delete('status');
        url.searchParams.delete('sort');
        url.searchParams.delete('page');
        
        // Add new params if not empty/default
        if (searchValue) url.searchParams.set('search', searchValue);
        if (roleValue && roleValue !== 'all') url.searchParams.set('role', roleValue);
        if (statusValue && statusValue !== 'all') url.searchParams.set('status', statusValue);
        if (sortValue && sortValue !== 'newest') url.searchParams.set('sort', sortValue);
        
        // Redirect with filters
        window.location.href = url.toString();
    }
    
    // Search input with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Search input event triggered');
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500); // 500ms delay
        });
        
        // Enter key for search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                applyFilters();
            }
        });
    } else {
        console.error('searchInput element not found!');
    }
    
    // Filter selects - immediate response
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    } else {
        console.error('roleFilter element not found!');
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    } else {
        console.error('statusFilter element not found!');
    }
    
    if (sortBy) {
        sortBy.addEventListener('change', applyFilters);
    } else {
        console.error('sortBy element not found!');
    }
});

