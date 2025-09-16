document.addEventListener('DOMContentLoaded', function() {
    console.log('Order Management JS loaded');
    
    // Get filter elements
    const searchInput = document.getElementById('searchInput');
    const orderStatusFilter = document.getElementById('orderStatusFilter');
    const orderTypeFilter = document.getElementById('orderTypeFilter');
    const paymentStatusFilter = document.getElementById('paymentStatusFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    // Debug - check if elements are found
    console.log('searchInput:', searchInput);
    console.log('orderStatusFilter:', orderStatusFilter);
    console.log('orderTypeFilter:', orderTypeFilter);
    console.log('paymentStatusFilter:', paymentStatusFilter);
    console.log('dateFilter:', dateFilter);
    
    // Function to apply filters
    function applyFilters() {
        console.log('Applying order filters...');
        const url = new URL(window.location.href);
        
        // Get current values
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const orderStatusValue = orderStatusFilter ? orderStatusFilter.value : '';
        const orderTypeValue = orderTypeFilter ? orderTypeFilter.value : '';
        const paymentStatusValue = paymentStatusFilter ? paymentStatusFilter.value : '';
        const dateValue = dateFilter ? dateFilter.value : '';
        
        console.log('Filter values:', {
            search: searchValue,
            orderStatus: orderStatusValue,
            orderType: orderTypeValue,
            paymentStatus: paymentStatusValue,
            date: dateValue
        });
        
        // Clear existing params
        url.searchParams.delete('search');
        url.searchParams.delete('order_status');
        url.searchParams.delete('order_type');
        url.searchParams.delete('payment_status');
        url.searchParams.delete('date');
        url.searchParams.delete('page'); // Reset pagination
        
        // Add new params if not empty
        if (searchValue) url.searchParams.set('search', searchValue);
        if (orderStatusValue) url.searchParams.set('order_status', orderStatusValue);
        if (orderTypeValue) url.searchParams.set('order_type', orderTypeValue);
        if (paymentStatusValue) url.searchParams.set('payment_status', paymentStatusValue);
        if (dateValue) url.searchParams.set('date', dateValue);
        
        console.log('Redirecting to:', url.toString());
        
        // Redirect with filters
        window.location.href = url.toString();
    }
    
    // Search input with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Search input event triggered:', this.value);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500); // 500ms delay
        });
        
        // Enter key for search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Enter key pressed');
                clearTimeout(searchTimeout);
                applyFilters();
            }
        });
    } else {
        console.error('searchInput element not found!');
    }
    
    // Order Status filter
    if (orderStatusFilter) {
        orderStatusFilter.addEventListener('change', function() {
            console.log('Order status changed to:', this.value);
            applyFilters();
        });
    } else {
        console.error('orderStatusFilter element not found!');
    }
    
    // Order Type filter
    if (orderTypeFilter) {
        orderTypeFilter.addEventListener('change', function() {
            console.log('Order type changed to:', this.value);
            applyFilters();
        });
    } else {
        console.error('orderTypeFilter element not found!');
    }
    
    // Payment Status filter
    if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', function() {
            console.log('Payment status changed to:', this.value);
            applyFilters();
        });
    } else {
        console.error('paymentStatusFilter element not found!');
    }
    
    // Date filter
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            console.log('Date changed to:', this.value);
            applyFilters();
        });
    } else {
        console.error('dateFilter element not found!');
    }
    
    // Clear filters function
    function clearAllFilters() {
        console.log('Clearing all filters');
        if (searchInput) searchInput.value = '';
        if (orderStatusFilter) orderStatusFilter.value = '';
        if (orderTypeFilter) orderTypeFilter.value = '';
        if (paymentStatusFilter) paymentStatusFilter.value = '';
        if (dateFilter) dateFilter.value = '';
        
        // Redirect to base URL without parameters
        const baseUrl = window.location.origin + window.location.pathname;
        window.location.href = baseUrl;
    }
    
    // Add clear filters button functionality if it exists
    const clearFiltersBtn = document.querySelector('.clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            clearAllFilters();
        });
    }
    
    // Auto-refresh for kitchen display (pending and preparing orders)
    const currentParams = new URLSearchParams(window.location.search);
    const orderStatus = currentParams.get('order_status');
    
    if (orderStatus === 'pending' || orderStatus === 'preparing') {
        console.log('Auto-refresh enabled for kitchen display');
        setTimeout(function() {
            console.log('Auto-refreshing page...');
            window.location.reload();
        }, 30000); // Refresh every 30 seconds
    }
    
    // Filter by status function (for quick filter buttons if needed)
    window.filterByStatus = function(status) {
        console.log('Quick filter by status:', status);
        if (orderStatusFilter) {
            orderStatusFilter.value = status;
            applyFilters();
        }
    };
    
    // Filter by type function
    window.filterByType = function(type) {
        console.log('Quick filter by type:', type);
        if (orderTypeFilter) {
            orderTypeFilter.value = type;
            applyFilters();
        }
    };
    
    // Filter by payment status function
    window.filterByPayment = function(paymentStatus) {
        console.log('Quick filter by payment:', paymentStatus);
        if (paymentStatusFilter) {
            paymentStatusFilter.value = paymentStatus;
            applyFilters();
        }
    };
});