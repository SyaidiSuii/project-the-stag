// Notification function
function showNotification(message, type) {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Handle form submission with loading state and notifications
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.querySelector('.user-form');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-save');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            // Let the form submit normally - don't prevent default
        });
    }
    
    // Check for success/error messages from session
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');
    
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

