// Promotion Edit Form JavaScript

let comboItemIndex = 2;
let bundleItemIndex = 2;
let menuItemsOptionsHTML = '';

// Initialize menu items (will be set from Blade)
function initializeMenuItems(menuItems) {
    menuItemsOptionsHTML = '<option value="">Select item...</option>';
    menuItems.forEach(item => {
        const price = parseFloat(item.price).toFixed(2);
        menuItemsOptionsHTML += `<option value="${item.id}">${item.name} (RM ${price})</option>`;
    });
}

// Set initial indexes
function setIndexes(comboIndex, bundleIndex) {
    comboItemIndex = comboIndex;
    bundleItemIndex = bundleIndex;
}

// Preview image
function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            const previewContainer = document.getElementById(previewId).parentElement;
            if (previewContainer) {
                previewContainer.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Add dynamic combo item row
function addComboItem(startIndex) {
    if (startIndex !== undefined) {
        comboItemIndex = startIndex;
    }

    const container = document.getElementById('comboItemsContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'combo-item-row';
    row.style = 'display: flex; gap: 12px; margin-bottom: 8px;';

    row.innerHTML = `
        <select name="promotion_data[combo_items][${comboItemIndex}][item_id]" class="form-control" style="flex: 1;" required>
            ${menuItemsOptionsHTML}
        </select>
        <input type="number" name="promotion_data[combo_items][${comboItemIndex}][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1" required>
        <button type="button" onclick="this.parentElement.remove()" class="btn-cancel" style="width: auto; padding: 8px 12px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
    comboItemIndex++;
}

// Add dynamic bundle item row
function addBundleItem(startIndex) {
    if (startIndex !== undefined) {
        bundleItemIndex = startIndex;
    }

    const container = document.getElementById('bundleItemsContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'bundle-item-row';
    row.style = 'display: flex; gap: 12px; margin-bottom: 8px;';

    row.innerHTML = `
        <select name="promotion_data[bundle_items][${bundleItemIndex}][item_id]" class="form-control" style="flex: 1;" required>
            ${menuItemsOptionsHTML}
        </select>
        <input type="number" name="promotion_data[bundle_items][${bundleItemIndex}][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1" required>
        <button type="button" onclick="this.parentElement.remove()" class="btn-cancel" style="width: auto; padding: 8px 12px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
    bundleItemIndex++;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase promo code
    const promoCodeInputs = document.querySelectorAll('#promo_code, #seasonal_promo_code');
    promoCodeInputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
        }
    });

    // Set minimum end date
    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const endDateInput = document.getElementById('end_date');
            if (endDateInput) {
                endDateInput.min = this.value;
                if (endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
            }
        });
    }
});
