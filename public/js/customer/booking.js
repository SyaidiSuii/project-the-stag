document.addEventListener('DOMContentLoaded', function() {
    // Get all table tiles
    const tableTiles = document.querySelectorAll('.table-tile');
    const selectedTableEl = document.getElementById('selectedTable');
    const selectedCapacityEl = document.getElementById('selectedCapacity');
    const selectionInfoEl = document.getElementById('selectionInfo');
    const bookWithMenuBtn = document.getElementById('bookWithMenu');
    const bookTableOnlyBtn = document.getElementById('bookTableOnly');
    
    let selectedTable = null;

    // Add click event to each table tile
    tableTiles.forEach(tile => {
        tile.addEventListener('click', function() {
            const status = this.dataset.status;
            
            // Don't allow selection if table is reserved or pending
            if (status === 'reserved' || status === 'pending') {
                return;
            }

            // Remove previous selection
            tableTiles.forEach(t => t.classList.remove('selected'));
            
            // Add selected class to clicked table
            this.classList.add('selected');
            
            // Get table data
            const tableId = this.dataset.id;
            const capacity = this.dataset.capacity;
            const isVVIP = this.classList.contains('vvip');
            
            // Update selected table info
            selectedTable = {
                id: tableId,
                capacity: capacity,
                isVVIP: isVVIP
            };
            
            // Update sidebar display
            selectedTableEl.textContent = tableId;
            selectedCapacityEl.textContent = `Capacity: ${capacity} guests`;
            
            // Add selection styling to info box
            selectionInfoEl.classList.add('has-selection');
            
            // Add VVIP styling if applicable
            if (isVVIP) {
                selectedTableEl.classList.add('vvip');
                selectedCapacityEl.classList.add('vvip');
                selectionInfoEl.classList.add('vvip-selection');
            } else {
                selectedTableEl.classList.remove('vvip');
                selectedCapacityEl.classList.remove('vvip');
                selectionInfoEl.classList.remove('vvip-selection');
            }
            
            // Enable booking buttons
            bookWithMenuBtn.disabled = false;
            bookTableOnlyBtn.disabled = false;
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableTiles.forEach(tile => {
                const tableId = tile.dataset.id.toLowerCase();
                if (tableId.includes(searchTerm)) {
                    tile.style.display = 'flex';
                } else {
                    tile.style.display = 'none';
                }
            });
            
            // Show/hide clear button
            clearBtn.style.display = searchTerm ? 'block' : 'none';
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            tableTiles.forEach(tile => {
                tile.style.display = 'flex';
            });
            this.style.display = 'none';
            searchInput.focus();
        });
    }

    // Filter tabs functionality
    const filterTabs = document.querySelectorAll('.tab[data-filter]');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active tab
            filterTabs.forEach(t => t.removeAttribute('aria-current'));
            this.setAttribute('aria-current', 'page');
            
            // Filter tables
            tableTiles.forEach(tile => {
                const status = tile.dataset.status;
                const isSelected = tile.classList.contains('selected');
                
                let shouldShow = false;
                
                switch(filter) {
                    case 'all':
                        shouldShow = true;
                        break;
                    case 'available':
                        shouldShow = status === 'available';
                        break;
                    case 'reserved':
                        shouldShow = status === 'reserved';
                        break;
                    case 'pending':
                        shouldShow = status === 'pending';
                        break;
                    case 'selected':
                        shouldShow = isSelected;
                        break;
                }
                
                tile.style.display = shouldShow ? 'flex' : 'none';
            });
        });
    });

    // Modal functionality
    const bookingModal = document.getElementById('bookingModal');
    const cancelBookingBtn = document.getElementById('cancelBooking');
    const confirmBookingBtn = document.getElementById('confirmBooking');

    // Book with menu button
    if (bookWithMenuBtn) {
        bookWithMenuBtn.addEventListener('click', function() {
            if (selectedTable) {
                showBookingModal('with-menu');
            }
        });
    }

    // Book table only button
    if (bookTableOnlyBtn) {
        bookTableOnlyBtn.addEventListener('click', function() {
            if (selectedTable) {
                showBookingModal('table-only');
            }
        });
    }

    // Cancel booking
    if (cancelBookingBtn) {
        cancelBookingBtn.addEventListener('click', function() {
            hideBookingModal();
        });
    }

    // Confirm booking
    if (confirmBookingBtn) {
        confirmBookingBtn.addEventListener('click', function() {
            submitBooking();
        });
    }

    function showBookingModal(type) {
        const bookingDate = document.getElementById('bookingDate').value;
        const bookingTime = document.getElementById('bookingTime').value;
        const guestCount = document.getElementById('guestCount').value;
        const guestName = document.getElementById('guestName').value;
        const guestEmail = document.getElementById('guestEmail').value;
        const guestPhone = document.getElementById('guestPhone').value;
        
        if (!bookingDate || !bookingTime || !guestCount || !guestName || !guestEmail || !guestPhone) {
            alert('Please fill in all booking details first.');
            return;
        }
        
        // Get cart total from the page
        const cartTotalText = document.getElementById('cartTotal').textContent;
        const cartTotal = parseFloat(cartTotalText.replace('RM ', '').replace(',', '')) || 0;
        
        // Calculate booking fee (if any)
        let bookingFee = 0;
        if (selectedTable.isVVIP) {
            bookingFee = 50.00; // VVIP booking fee
        }
        
        // Calculate total
        const totalAmount = cartTotal + bookingFee;
        
        // Update modal summary
        document.getElementById('summaryTable').textContent = selectedTable.id;
        document.getElementById('summaryDate').textContent = formatDate(bookingDate);
        document.getElementById('summaryTime').textContent = formatTime(bookingTime);
        document.getElementById('summaryGuests').textContent = `${guestCount} guest${guestCount > 1 ? 's' : ''}`;
        
        // Update booking type display
        const bookingTypeEl = document.getElementById('summaryBookingType');
        if (bookingTypeEl) {
            bookingTypeEl.textContent = type === 'with-menu' ? 'Table + Menu' : 'Table Only';
        }
        
        // Store booking type for submission
        bookingModal.dataset.bookingType = type;
        
        // Update cost breakdown
        const foodTotalEl = document.getElementById('summaryFoodTotal');
        const bookingFeeEl = document.getElementById('summaryBookingFee');
        
        if (foodTotalEl) {
            if (type === 'with-menu' && cartTotal > 0) {
                foodTotalEl.style.display = 'flex';
                foodTotalEl.querySelector('strong').textContent = `RM ${cartTotal.toFixed(2)}`;
            } else {
                foodTotalEl.style.display = 'none';
            }
        }
        
        if (bookingFeeEl) {
            if (bookingFee > 0) {
                bookingFeeEl.style.display = 'flex';
                bookingFeeEl.querySelector('strong').textContent = `RM ${bookingFee.toFixed(2)}`;
            } else {
                bookingFeeEl.style.display = 'none';
            }
        }
        
        // Update total
        document.getElementById('summaryTotal').textContent = `RM ${totalAmount.toFixed(2)}`;
        
        // Show modal
        bookingModal.style.display = 'flex';
    }
    
    function submitBooking() {
        // Get form data
        const bookingDate = document.getElementById('bookingDate').value;
        const bookingTime = document.getElementById('bookingTime').value;
        const guestCount = document.getElementById('guestCount').value;
        const guestName = document.getElementById('guestName').value;
        const guestEmail = document.getElementById('guestEmail').value;
        const guestPhone = document.getElementById('guestPhone').value;
        const bookingType = bookingModal.dataset.bookingType;
        
        // Validate required fields
        if (!selectedTable || !bookingDate || !bookingTime || !guestCount || !guestName || !guestEmail || !guestPhone || !bookingType) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Disable confirm button to prevent double submission
        confirmBookingBtn.disabled = true;
        confirmBookingBtn.textContent = 'Processing...';
        
        // Prepare form data
        const formData = new FormData();
        formData.append('table_id', selectedTable.id);
        formData.append('booking_date', bookingDate);
        formData.append('booking_time', bookingTime);
        formData.append('party_size', guestCount);
        formData.append('guest_name', guestName);
        formData.append('guest_email', guestEmail);
        formData.append('guest_phone', guestPhone);
        formData.append('booking_type', bookingType);
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        // Submit booking
        fetch('/customer/booking/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert(`Booking confirmed! Confirmation code: ${data.reservation.confirmation_code}`);
                
                // Redirect to orders page
                window.location.href = data.redirect_url;
            } else {
                // Show error message
                alert(data.message || 'Failed to create booking. Please try again.');
                
                // Re-enable confirm button
                confirmBookingBtn.disabled = false;
                confirmBookingBtn.textContent = 'Confirm Booking';
            }
        })
        .catch(error => {
            console.error('Booking submission error:', error);
            alert('Failed to create booking. Please try again.');
            
            // Re-enable confirm button
            confirmBookingBtn.disabled = false;
            confirmBookingBtn.textContent = 'Confirm Booking';
        });
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        return date.toLocaleDateString('en-US', options);
    }
    
    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const time = new Date();
        time.setHours(parseInt(hours), parseInt(minutes));
        
        return time.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    function hideBookingModal() {
        bookingModal.style.display = 'none';
    }

    // Close modal when clicking outside
    bookingModal?.addEventListener('click', function(e) {
        if (e.target === this) {
            hideBookingModal();
        }
    });

    // Cart toggle functionality
    const cartHeader = document.getElementById('cartHeader');
    const cartContent = document.getElementById('cartContent');
    const cartToggleIcon = document.getElementById('cartToggleIcon');

    if (cartHeader) {
        cartHeader.addEventListener('click', function() {
            cartContent.classList.toggle('open');
            
            if (cartContent.classList.contains('open')) {
                cartToggleIcon.innerHTML = '<i class="fas fa-chevron-down"></i>';
            } else {
                cartToggleIcon.innerHTML = '<i class="fas fa-chevron-up"></i>';
            }
        });
    }
});