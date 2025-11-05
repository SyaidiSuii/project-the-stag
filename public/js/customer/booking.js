document.addEventListener("DOMContentLoaded", function () {
    // Get all table tiles
    const tableTiles = document.querySelectorAll(".table-tile");
    const selectedTableEl = document.getElementById("selectedTable");
    const selectedCapacityEl = document.getElementById("selectedCapacity");
    const selectionInfoEl = document.getElementById("selectionInfo");
    const bookWithMenuBtn = document.getElementById("bookWithMenu");
    const bookTableOnlyBtn = document.getElementById("bookTableOnly");
    const bookingDateInput = document.getElementById("bookingDate");
    const bookingTimeInput = document.getElementById("bookingTime");
    const guestCountInput = document.getElementById("guestCount");

    let selectedTable = null;
    let availabilityCheckTimeout = null;

    // Add click event to each table tile
    tableTiles.forEach((tile) => {
        tile.addEventListener("click", function () {
            const status = this.dataset.status;

            // Don't allow selection if table is not available
            // (reserved, pending, occupied, or in maintenance)
            if (
                status === "reserved" ||
                status === "pending" ||
                status === "occupied" ||
                status === "maintenance"
            ) {
                // Show tooltip why it's not available
                if (typeof Toast !== "undefined") {
                    let message = "";
                    switch (status) {
                        case "reserved":
                            message = "This table is already reserved";
                            break;
                        case "pending":
                            message = "This table has a pending reservation";
                            break;
                        case "occupied":
                            message = "This table is currently occupied";
                            break;
                        case "maintenance":
                            message = "This table is under maintenance";
                            break;
                    }
                    Toast.warning("Table Unavailable", message);
                }
                return;
            }

            // Remove previous selection
            tableTiles.forEach((t) => {
                t.classList.remove("selected");
                // Restore original status text
                const statusBadge = t.querySelector(".table-status-badge");
                if (statusBadge) {
                    statusBadge.textContent = t.dataset.status.toUpperCase();
                }
            });

            // Add selected class to clicked table
            this.classList.add("selected");

            // Change status badge text to SELECTED
            const statusBadge = this.querySelector(".table-status-badge");
            if (statusBadge) {
                statusBadge.textContent = "SELECTED";
            }

            // Get table data
            const tableId = this.dataset.id;
            const capacity = this.dataset.capacity;
            const isVVIP = this.classList.contains("vvip");

            console.log("🏛️ Table Selected:", {
                id: tableId,
                capacity: capacity,
                isVVIP: isVVIP,
                classList: this.classList.toString(),
            });

            // Update selected table info
            selectedTable = {
                id: tableId,
                capacity: capacity,
                isVVIP: isVVIP,
            };

            // Update sidebar display
            selectedTableEl.textContent = tableId;
            selectedCapacityEl.textContent = `Capacity: ${capacity} guests`;

            // Add selection styling to info box
            selectionInfoEl.classList.add("has-selection");

            // Add VVIP styling if applicable
            if (isVVIP) {
                selectedTableEl.classList.add("vvip");
                selectedCapacityEl.classList.add("vvip");
                selectionInfoEl.classList.add("vvip-selection");
            } else {
                selectedTableEl.classList.remove("vvip");
                selectedCapacityEl.classList.remove("vvip");
                selectionInfoEl.classList.remove("vvip-selection");
            }

            // Check availability if date and time are selected
            checkAvailabilityIfReady();

            // Enable booking buttons
            updateBookingButtonsState();
        });
    });

    // Real-time validation for party size
    if (guestCountInput) {
        guestCountInput.addEventListener("change", function () {
            if (selectedTable) {
                const partySize = parseInt(this.value);
                const tableCapacity = parseInt(selectedTable.capacity);

                if (partySize > tableCapacity) {
                    if (typeof Toast !== "undefined") {
                        Toast.warning(
                            "Party Size Exceeds Capacity",
                            `This table can only accommodate ${tableCapacity} guests. Please select a larger table or reduce party size.`
                        );
                    }
                    updateBookingButtonsState();
                    return;
                }
            }
            checkAvailabilityIfReady();
        });
    }

    // Real-time availability checking when date/time changes
    if (bookingDateInput) {
        bookingDateInput.addEventListener("change", function () {
            validateBookingDate();
            checkAvailabilityIfReady();
        });
    }

    if (bookingTimeInput) {
        bookingTimeInput.addEventListener("change", function () {
            validateBookingTime();
            checkAvailabilityIfReady();
        });
    }

    function validateBookingDate() {
        const selectedDate = new Date(bookingDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            if (typeof Toast !== "undefined") {
                Toast.warning(
                    "Invalid Date",
                    "Please select a future date for your reservation."
                );
            }
            bookingDateInput.value = "";
            return false;
        }
        return true;
    }

    function validateBookingTime() {
        if (!bookingDateInput.value || !bookingTimeInput.value) return true;

        const bookingDateTime = new Date(
            bookingDateInput.value + " " + bookingTimeInput.value
        );
        const minBookingTime = new Date();
        minBookingTime.setHours(minBookingTime.getHours() + 1);

        if (bookingDateTime < minBookingTime) {
            if (typeof Toast !== "undefined") {
                Toast.warning(
                    "Invalid Time",
                    "Reservations must be made at least 1 hour in advance."
                );
            }
            bookingTimeInput.value = "";
            return false;
        }
        return true;
    }

    function checkAvailabilityIfReady() {
        if (
            !selectedTable ||
            !bookingDateInput.value ||
            !bookingTimeInput.value
        ) {
            return;
        }

        // Debounce the availability check
        clearTimeout(availabilityCheckTimeout);
        availabilityCheckTimeout = setTimeout(() => {
            checkTableAvailability();
        }, 500);
    }

    function checkTableAvailability() {
        const partySize = guestCountInput
            ? parseInt(guestCountInput.value)
            : null;

        const formData = new FormData();
        formData.append("table_id", selectedTable.id);
        formData.append("booking_date", bookingDateInput.value);
        formData.append("booking_time", bookingTimeInput.value);
        if (partySize) {
            formData.append("party_size", partySize);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append("_token", csrfToken.getAttribute("content"));
        }

        fetch("/customer/booking/check-availability", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.available) {
                    // Table is available
                    updateBookingButtonsState(true);

                    // Update time slots dropdown if available
                    if (
                        data.available_slots &&
                        data.available_slots.length > 0
                    ) {
                        updateTimeSlotOptions(data.available_slots);
                    }
                } else {
                    // Table is not available
                    updateBookingButtonsState(false);

                    if (typeof Toast !== "undefined") {
                        Toast.error(
                            "Table Not Available",
                            data.message ||
                                "This table is not available for the selected date/time."
                        );
                    }

                    // Suggest alternative time slots if available
                    if (
                        data.available_slots &&
                        data.available_slots.length > 0
                    ) {
                        updateTimeSlotOptions(data.available_slots);
                    }
                }
            })
            .catch((error) => {
                console.error("Availability check error:", error);
                // Don't disable buttons on network error, let server-side validation handle it
            });
    }

    function updateTimeSlotOptions(availableSlots) {
        if (!bookingTimeInput) return;

        const currentValue = bookingTimeInput.value;
        const options = bookingTimeInput.querySelectorAll(
            'option:not([value=""])'
        );

        options.forEach((option) => {
            const timeValue = option.value;
            if (availableSlots.includes(timeValue)) {
                option.disabled = false;
                option.style.color = "";
            } else {
                option.disabled = true;
                option.style.color = "#ccc";
            }
        });
    }

    function updateBookingButtonsState(isAvailable = true) {
        const partySize = guestCountInput ? parseInt(guestCountInput.value) : 0;
        const tableCapacity = selectedTable
            ? parseInt(selectedTable.capacity)
            : 0;

        const isValid =
            selectedTable &&
            bookingDateInput.value &&
            bookingTimeInput.value &&
            partySize > 0 &&
            partySize <= tableCapacity &&
            isAvailable;

        if (bookWithMenuBtn) bookWithMenuBtn.disabled = !isValid;
        if (bookTableOnlyBtn) bookTableOnlyBtn.disabled = !isValid;
    }

    // Search functionality
    const searchInput = document.getElementById("searchInput");
    const clearBtn = document.getElementById("clearSearch");

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const searchTerm = this.value.toLowerCase();

            tableTiles.forEach((tile) => {
                const tableId = tile.dataset.id.toLowerCase();
                if (tableId.includes(searchTerm)) {
                    tile.style.display = "flex";
                } else {
                    tile.style.display = "none";
                }
            });

            // Show/hide clear button
            clearBtn.style.display = searchTerm ? "block" : "none";
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            searchInput.value = "";
            tableTiles.forEach((tile) => {
                tile.style.display = "flex";
            });
            this.style.display = "none";
            searchInput.focus();
        });
    }

    // Filter tabs functionality
    const filterTabs = document.querySelectorAll(".tab[data-filter]");

    filterTabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            const filter = this.dataset.filter;

            // Update active tab
            filterTabs.forEach((t) => t.removeAttribute("aria-current"));
            this.setAttribute("aria-current", "page");

            // Filter tables
            tableTiles.forEach((tile) => {
                const status = tile.dataset.status;
                const isSelected = tile.classList.contains("selected");

                let shouldShow = false;

                switch (filter) {
                    case "all":
                        shouldShow = true;
                        break;
                    case "available":
                        shouldShow = status === "available";
                        break;
                    case "occupied":
                        shouldShow = status === "occupied";
                        break;
                    case "reserved":
                        shouldShow = status === "reserved";
                        break;
                    case "pending":
                        shouldShow = status === "pending";
                        break;
                    case "maintenance":
                        shouldShow = status === "maintenance";
                        break;
                    case "selected":
                        shouldShow = isSelected;
                        break;
                }

                tile.style.display = shouldShow ? "flex" : "none";
            });
        });
    });

    // Modal functionality
    const bookingModal = document.getElementById("bookingModal");
    const cancelBookingBtn = document.getElementById("cancelBooking");
    const confirmBookingBtn = document.getElementById("confirmBooking");

    // Book with menu button
    if (bookWithMenuBtn) {
        bookWithMenuBtn.addEventListener("click", function () {
            if (selectedTable) {
                // Check if cart has items
                const cartCountEl = document.getElementById("cartCount");
                const cartCount = cartCountEl ? parseInt(cartCountEl.textContent) || 0 : 0;

                if (cartCount === 0) {
                    // Show modern toast notification that cart is empty
                    if (typeof Toast !== "undefined") {
                        Toast.warning(
                            "Cart is Empty",
                            "Please add menu items before booking with menu."
                        );
                    } else {
                        alert("Your cart is empty! Please add menu items before booking with menu.");
                    }
                    return;
                }

                showBookingModal("with-menu");
            }
        });
    }

    // Book table only button
    if (bookTableOnlyBtn) {
        bookTableOnlyBtn.addEventListener("click", function () {
            if (selectedTable) {
                showBookingModal("table-only");
            }
        });
    }

    // Cancel booking
    if (cancelBookingBtn) {
        cancelBookingBtn.addEventListener("click", function () {
            hideBookingModal();
        });
    }

    // Confirm booking
    if (confirmBookingBtn) {
        confirmBookingBtn.addEventListener("click", function () {
            submitBooking();
        });
    }

    function showBookingModal(type) {
        const bookingDate = document.getElementById("bookingDate").value;
        const bookingTime = document.getElementById("bookingTime").value;
        const guestCount = document.getElementById("guestCount").value;
        const guestName = document.getElementById("guestName").value;
        const guestEmail = document.getElementById("guestEmail").value;
        const guestPhone = document.getElementById("guestPhone").value;

        if (
            !bookingDate ||
            !bookingTime ||
            !guestCount ||
            !guestName ||
            !guestEmail ||
            !guestPhone
        ) {
            if (typeof Toast !== "undefined") {
                Toast.warning(
                    "Incomplete Details",
                    "Please fill in all booking details first."
                );
            } else {
                alert("Please fill in all booking details first.");
            }
            return;
        }

        // Get cart total from the page
        const cartTotalText = document.getElementById("cartTotal").textContent;
        const cartTotal =
            parseFloat(cartTotalText.replace("RM ", "").replace(",", "")) || 0;

        console.log("🔍 DEBUG - Cart Total:", cartTotal);
        console.log("🔍 DEBUG - Selected Table:", selectedTable);
        console.log("🔍 DEBUG - Is VVIP?:", selectedTable.isVVIP);

        // Calculate booking fee (if any)
        let bookingFee = 0;
        if (selectedTable.isVVIP) {
            // VVIP booking fee: RM 10 per hour (default 1 hour minimum)
            const durationHours = 1; // Default booking duration
            bookingFee = 10.0 * durationHours; // RM 10 total
            console.log(
                "✅ VVIP Fee Calculated:",
                bookingFee,
                "for",
                durationHours,
                "hours"
            );
        } else {
            console.log("❌ Not VVIP table - no fee");
        }

        console.log("💰 Final Booking Fee:", bookingFee);

        // Calculate total
        const totalAmount = cartTotal + bookingFee;

        console.log("💵 Total Amount (Cart + Fee):", totalAmount);

        // Update modal summary
        document.getElementById("summaryTable").textContent = selectedTable.id;
        document.getElementById("summaryDate").textContent =
            formatDate(bookingDate);
        document.getElementById("summaryTime").textContent =
            formatTime(bookingTime);
        document.getElementById(
            "summaryGuests"
        ).textContent = `${guestCount} guest${guestCount > 1 ? "s" : ""}`;

        // Update booking type display
        const bookingTypeEl = document.getElementById("summaryBookingType");
        if (bookingTypeEl) {
            bookingTypeEl.textContent =
                type === "with-menu" ? "Table + Menu" : "Table Only";
        }

        // Store booking type for submission
        bookingModal.dataset.bookingType = type;

        // Update cost breakdown
        const foodTotalEl = document.getElementById("summaryFoodTotal");
        const bookingFeeEl = document.getElementById("summaryBookingFee");

        if (foodTotalEl) {
            if (type === "with-menu" && cartTotal > 0) {
                foodTotalEl.style.display = "flex";
                foodTotalEl.querySelector(
                    "strong"
                ).textContent = `RM ${cartTotal.toFixed(2)}`;
            } else {
                foodTotalEl.style.display = "none";
            }
        }

        if (bookingFeeEl) {
            if (bookingFee > 0) {
                bookingFeeEl.style.display = "flex";
                const feeAmount = `RM ${bookingFee.toFixed(2)}`;
                bookingFeeEl.querySelector("strong").textContent = feeAmount;
                console.log("📝 Updated Booking Fee Display to:", feeAmount);
            } else {
                bookingFeeEl.style.display = "none";
                console.log("🚫 Hiding booking fee (zero)");
            }
        } else {
            console.error("❗ bookingFeeEl not found!");
        }

        // Update total
        const totalText = `RM ${totalAmount.toFixed(2)}`;
        document.getElementById("summaryTotal").textContent = totalText;
        console.log("📝 Updated Total Display to:", totalText);

        // Show modal with accessibility support
        bookingModal.style.display = "flex";
        bookingModal.setAttribute("aria-hidden", "false");

        // Store previously focused element
        bookingModal.dataset.previousFocus = document.activeElement.id || "";

        // Focus the confirm button
        setTimeout(() => {
            if (confirmBookingBtn) {
                confirmBookingBtn.focus();
            }
        }, 100);

        // Enable focus trap
        enableFocusTrap();
    }

    function enableFocusTrap() {
        const modalContent = bookingModal.querySelector(".modal-content");
        if (!modalContent) return;

        const focusableElements = modalContent.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        function handleTabKey(e) {
            if (e.key !== "Tab") return;

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    lastFocusable.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    firstFocusable.focus();
                    e.preventDefault();
                }
            }
        }

        function handleEscapeKey(e) {
            if (e.key === "Escape") {
                hideBookingModal();
            }
        }

        bookingModal.addEventListener("keydown", handleTabKey);
        bookingModal.addEventListener("keydown", handleEscapeKey);

        // Store handlers for cleanup
        bookingModal.dataset.tabHandler = "attached";
    }

    function submitBooking() {
        // Get form data
        const bookingDate = document.getElementById("bookingDate").value;
        const bookingTime = document.getElementById("bookingTime").value;
        const guestCount = document.getElementById("guestCount").value;
        const guestName = document.getElementById("guestName").value;
        const guestEmail = document.getElementById("guestEmail").value;
        const guestPhone = document.getElementById("guestPhone").value;
        const bookingType = bookingModal.dataset.bookingType;

        // Validate required fields
        if (
            !selectedTable ||
            !bookingDate ||
            !bookingTime ||
            !guestCount ||
            !guestName ||
            !guestEmail ||
            !guestPhone ||
            !bookingType
        ) {
            Toast.warning(
                "Missing Information",
                "Please fill in all required fields."
            );
            return;
        }

        // Disable confirm button to prevent double submission
        confirmBookingBtn.disabled = true;
        confirmBookingBtn.textContent = "Processing...";

        // Prepare form data
        const formData = new FormData();
        formData.append("table_id", selectedTable.id);
        formData.append("booking_date", bookingDate);
        formData.append("booking_time", bookingTime);
        formData.append("party_size", guestCount);
        formData.append("guest_name", guestName);
        formData.append("guest_email", guestEmail);
        formData.append("guest_phone", guestPhone);
        formData.append("booking_type", bookingType);

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append("_token", csrfToken.getAttribute("content"));
        }

        // Submit booking
        fetch("/customer/booking/store", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .then((response) => {
                // Handle both successful and error responses
                if (!response.ok) {
                    return response.json().then((err) => {
                        throw new Error(err.message || "Booking failed");
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    // Hide modal
                    hideBookingModal();

                    // Show success message
                    if (typeof Toast !== "undefined") {
                        Toast.success(
                            "Booking Confirmed!",
                            `Confirmation code: ${data.reservation.confirmation_code}`,
                            3000
                        );
                    }

                    // Redirect to orders page after brief delay
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    // Show error message
                    if (typeof Toast !== "undefined") {
                        Toast.error(
                            "Booking Failed",
                            data.message ||
                                "Failed to create booking. Please try again."
                        );
                    } else {
                        alert(
                            "Booking Failed: " +
                                (data.message ||
                                    "Failed to create booking. Please try again.")
                        );
                    }

                    // Re-enable confirm button
                    confirmBookingBtn.disabled = false;
                    confirmBookingBtn.textContent = "Confirm Booking";
                }
            })
            .catch((error) => {
                console.error("Booking submission error:", error);

                if (typeof Toast !== "undefined") {
                    Toast.error(
                        "Booking Failed",
                        error.message ||
                            "Network error. Please check your connection and try again."
                    );
                } else {
                    alert(
                        "Booking Failed: " +
                            (error.message ||
                                "Network error. Please try again.")
                    );
                }

                // Re-enable confirm button
                confirmBookingBtn.disabled = false;
                confirmBookingBtn.textContent = "Confirm Booking";
            });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        };
        return date.toLocaleDateString("en-US", options);
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(":");
        const time = new Date();
        time.setHours(parseInt(hours), parseInt(minutes));

        return time.toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
    }

    function hideBookingModal() {
        bookingModal.style.display = "none";
        bookingModal.setAttribute("aria-hidden", "true");

        // Restore focus to previously focused element
        const previousFocusId = bookingModal.dataset.previousFocus;
        if (previousFocusId) {
            const previousElement = document.getElementById(previousFocusId);
            if (previousElement) {
                previousElement.focus();
            }
        }
    }

    // Close modal when clicking outside
    bookingModal?.addEventListener("click", function (e) {
        if (e.target === this) {
            hideBookingModal();
        }
    });

    // Cart toggle functionality
    const cartHeader = document.getElementById("cartHeader");
    const cartContent = document.getElementById("cartContent");
    const cartToggleIcon = document.getElementById("cartToggleIcon");

    if (cartHeader) {
        cartHeader.addEventListener("click", function () {
            cartContent.classList.toggle("open");

            if (cartContent.classList.contains("open")) {
                cartToggleIcon.innerHTML =
                    '<i class="fas fa-chevron-down"></i>';
            } else {
                cartToggleIcon.innerHTML = '<i class="fas fa-chevron-up"></i>';
            }
        });
    }

    // Floating booking button and sidebar toggle
    const floatingBtn = document.getElementById("floatingBookingBtn");
    const bookingSidebar = document.getElementById("bookingSidebar");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const floatingButtonText = document.getElementById("floatingButtonText");

    // Update button text when table is selected
    function updateFloatingBadge() {
        if (selectedTable && floatingButtonText) {
            floatingButtonText.textContent = `Selected Table: ${selectedTable.id}`;
        } else if (floatingButtonText) {
            floatingButtonText.textContent = "Select Table";
        }
    }

    // Toggle sidebar
    function toggleBookingSidebar() {
        const isActive = bookingSidebar.classList.contains("active");

        if (isActive) {
            bookingSidebar.classList.remove("active");
            sidebarOverlay.classList.remove("active");
        } else {
            bookingSidebar.classList.add("active");
            sidebarOverlay.classList.add("active");
        }
    }

    // Floating button click
    if (floatingBtn) {
        floatingBtn.addEventListener("click", function () {
            toggleBookingSidebar();
        });
    }

    // Overlay click to close
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", function () {
            toggleBookingSidebar();
        });
    }

    // Update the table selection to also update the badge
    const originalTableClickHandler = tableTiles.forEach;
    tableTiles.forEach((tile) => {
        tile.addEventListener("click", function () {
            // Wait a bit for the selection to update
            setTimeout(() => {
                updateFloatingBadge();
            }, 100);
        });
    });

    // Initialize badge state
    updateFloatingBadge();
});
