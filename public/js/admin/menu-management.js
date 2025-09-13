document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu Management JS loaded');
    
    // Get filter elements
    const searchInput = document.getElementById('searchInput');
    const mainCategoryFilter = document.getElementById('mainCategoryFilter');
    const subCategoryFilter = document.getElementById('subCategoryFilter');
    const availabilityFilter = document.getElementById('availabilityFilter');
    const featuredFilter = document.getElementById('featuredFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    // Debug - check if elements are found
    console.log('searchInput:', searchInput);
    console.log('mainCategoryFilter:', mainCategoryFilter);
    console.log('subCategoryFilter:', subCategoryFilter);
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
        const subCategoryValue = subCategoryFilter ? subCategoryFilter.value : '';
        const availabilityValue = availabilityFilter ? availabilityFilter.value : '';
        const featuredValue = featuredFilter ? featuredFilter.value : '';
        const sortValue = sortFilter ? sortFilter.value : 'name';
        
        // Clear existing params
        url.searchParams.delete('search');
        url.searchParams.delete('main_category_id');
        url.searchParams.delete('category_id');
        url.searchParams.delete('availability');
        url.searchParams.delete('is_featured');
        url.searchParams.delete('sort_by');
        url.searchParams.delete('page');
        
        // Add new params if not empty/default
        if (searchValue) url.searchParams.set('search', searchValue);
        if (mainCategoryValue) url.searchParams.set('main_category_id', mainCategoryValue);
        if (subCategoryValue) url.searchParams.set('category_id', subCategoryValue);
        if (availabilityValue) url.searchParams.set('availability', availabilityValue);
        if (featuredValue) url.searchParams.set('is_featured', featuredValue);
        if (sortValue && sortValue !== 'name') url.searchParams.set('sort_by', sortValue);
        
        // Redirect with filters
        window.location.href = url.toString();
    }
    
    // Function to load sub categories via AJAX
    function loadSubCategories(mainCategoryId) {
        console.log('Loading sub categories for main category:', mainCategoryId);
        
        if (!mainCategoryId) {
            // Clear sub categories if no main category selected
            if (subCategoryFilter) {
                subCategoryFilter.innerHTML = '<option value="">All Sub Categories</option>';
            }
            return;
        }
        
        // AJAX call to get sub categories
        fetch(`/admin/menu-items/sub-categories?main_category_id=${mainCategoryId}`)
            .then(response => response.json())
            .then(subCategories => {
                console.log('Received sub categories:', subCategories);
                
                if (subCategoryFilter) {
                    // Clear existing options
                    subCategoryFilter.innerHTML = '<option value="">All Sub Categories</option>';
                    
                    // Add sub categories
                    subCategories.forEach(subCategory => {
                        const option = document.createElement('option');
                        option.value = subCategory.id;
                        option.textContent = subCategory.name;
                        subCategoryFilter.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading sub categories:', error);
            });
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
    
    // Main category filter - load sub categories and don't apply filters immediately
    if (mainCategoryFilter) {
        mainCategoryFilter.addEventListener('change', function() {
            console.log('Main category changed');
            const mainCategoryId = this.value;
            
            // Load sub categories for selected main category
            loadSubCategories(mainCategoryId);
            
            // Clear sub category selection
            if (subCategoryFilter) {
                subCategoryFilter.value = '';
            }
            
            // Apply filters after loading sub categories
            setTimeout(applyFilters, 100); // Small delay to ensure sub categories are cleared
        });
    } else {
        console.error('mainCategoryFilter element not found!');
    }
    
    // Sub category filter - immediate response
    if (subCategoryFilter) {
        subCategoryFilter.addEventListener('change', applyFilters);
    } else {
        console.error('subCategoryFilter element not found!');
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