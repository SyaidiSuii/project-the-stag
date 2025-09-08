// ===== UI Interactions & Animations (Keep) =====

// ========== Navigation Active State ==========
function initializeNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// ========== Search Bar ==========
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    if (!searchInput) return;

    const performSearch = () => {
        const currentCategoryTab = document.querySelector('.tab[aria-current="page"]');
        const category = currentCategoryTab ? currentCategoryTab.dataset.category : 'All';
        // NOTE: di Laravel, filter dah buat di backend atau guna JS di Blade
        const cards = document.querySelectorAll('.food-card');
        let anyVisible = false;

        cards.forEach(card => {
            const name = card.dataset.name?.toLowerCase() || '';
            const matchesSearch = name.includes(searchInput.value.toLowerCase());
            if (matchesSearch) {
                card.style.display = '';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('noResults').style.display = anyVisible ? 'none' : 'block';
        if (clearSearch) clearSearch.style.display = searchInput.value ? 'block' : 'none';
    };

    searchInput.addEventListener('input', performSearch);
    if (clearSearch) {
        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            performSearch();
        });
    }
}

// ========== Category Tabs ==========
function initializeTabs(tabs) {
    if (!tabs || tabs.length === 0) return;
    const initialTab = document.querySelector('.tab[aria-current="page"]') || tabs[0];
    if (initialTab) {
        const cat = initialTab.dataset.category;
        document.getElementById('categoryTitle').textContent = cat;
    }
    tabs.forEach(tab => tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.removeAttribute('aria-current'));
        tab.setAttribute('aria-current', 'page');
        const cat = tab.dataset.category;
        document.getElementById('categoryTitle').textContent = cat;

        // Filter ikut category
        document.querySelectorAll('.category-section').forEach(section => {
            if (cat === 'All' || section.dataset.category === cat) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });
    }));
}

// ========== Addon Modal ==========
function setModalOpen(open) {
    const modal = document.getElementById('addon-modal');
    if (!modal) return;
    modal.style.display = open ? 'flex' : 'none';
    if (open) updateAddonLabels();
}

function updateAddonLabels() {
    document.querySelectorAll('.addon-options label').forEach(label => {
        const checkbox = label.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) label.classList.add('checked');
        else label.classList.remove('checked');
    });
}

document.addEventListener('change', (e) => {
    if (e.target.type === 'checkbox' && e.target.closest('.addon-options')) {
        updateAddonLabels();
    }
});

// ========== Cart Modal Open/Close ==========
function initializeFABCart() {
    const cartFab = document.getElementById('cartFab');
    const cartModal = document.getElementById('cartModal');
    const closeBtn = document.getElementById('cartModalClose');
    const backdrop = document.getElementById('cartModalBackdrop');

    if (cartFab) {
        cartFab.addEventListener('click', () => {
            cartModal.classList.add('open');
            cartFab.classList.add('bounce');
            setTimeout(() => cartFab.classList.remove('bounce'), 600);
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            cartModal.classList.remove('open');
        });
    }
    if (backdrop) {
        backdrop.addEventListener('click', () => {
            cartModal.classList.remove('open');
        });
    }
}

// ========== Confirmation Modal ==========
function showConfirmation(title, text, confirmText) {
    return new Promise(resolve => {
        const modal = document.getElementById('confirmation-modal');
        const cancelBtn = document.getElementById('confirm-cancel-btn');
        const confirmBtn = document.getElementById('confirm-action-btn');

        modal.querySelector('.confirmation-modal-title').textContent = title;
        modal.querySelector('.confirmation-modal-text').textContent = text;
        confirmBtn.textContent = confirmText;

        modal.classList.add('open');

        cancelBtn.onclick = () => {
            modal.classList.remove('open');
            resolve(false);
        };
        confirmBtn.onclick = () => {
            modal.classList.remove('open');
            resolve(true);
        };
    });
}

// ========== Init ==========
document.addEventListener('DOMContentLoaded', () => {
    initializeNavigation();
    initializeSearch();
    initializeFABCart();
    const tabs = document.querySelectorAll('.tab');
    initializeTabs(tabs);
});
