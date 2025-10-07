document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu Management JS loaded');
    
    // Get filter elements
    const searchInput = document.getElementById('searchInput');
    const mainCategoryFilter = document.getElementById('mainCategoryFilter');
    const availabilityFilter = document.getElementById('availabilityFilter');
    const featuredFilter = document.getElementById('featuredFilter');
    const sortFilter = document.getElementById('sortFilter');

    // Debug - check if elements are found
    console.log('searchInput:', searchInput);
    console.log('mainCategoryFilter:', mainCategoryFilter);
    console.log('availabilityFilter:', availabilityFilter);
    console.log('featuredFilter:', featuredFilter);
    console.log('sortFilter:', sortFilter);
    
    // Function to apply filters
    function applyFilters() {
        console.log('Applying menu filters...');
        const url = new URL(window.location.href);
        
        // Get current values
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const mainCategoryValue = mainCategoryFilter ? mainCategoryFilter.value : '';
        const availabilityValue = availabilityFilter ? availabilityFilter.value : '';
        const featuredValue = featuredFilter ? featuredFilter.value : '';
        const sortValue = sortFilter ? sortFilter.value : 'name';

        // Clear existing params
        url.searchParams.delete('search');
        url.searchParams.delete('category_id');
        url.searchParams.delete('availability');
        url.searchParams.delete('is_featured');
        url.searchParams.delete('sort_by');
        url.searchParams.delete('page');

        // Add new params if not empty/default
        if (searchValue) url.searchParams.set('search', searchValue);
        if (mainCategoryValue) url.searchParams.set('category_id', mainCategoryValue);
        if (availabilityValue) url.searchParams.set('availability', availabilityValue);
        if (featuredValue) url.searchParams.set('is_featured', featuredValue);
        if (sortValue && sortValue !== 'name') url.searchParams.set('sort_by', sortValue);
        
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
    
    // Main category filter
    if (mainCategoryFilter) {
        mainCategoryFilter.addEventListener('change', applyFilters);
    } else {
        console.error('mainCategoryFilter element not found!');
    }
    
    if (availabilityFilter) {
        availabilityFilter.addEventListener('change', applyFilters);
    } else {
        console.error('availabilityFilter element not found!');
    }
    
    if (featuredFilter) {
        featuredFilter.addEventListener('change', applyFilters);
    } else {
        console.error('featuredFilter element not found!');
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', applyFilters);
    } else {
        console.error('sortFilter element not found!');
    }
});