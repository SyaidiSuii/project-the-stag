document.addEventListener('DOMContentLoaded', () => {
    // Set current date
    document.getElementById('currentDate').textContent =
        new Date().toLocaleDateString('en-MY', {
            weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
        });

    // View site button
    document.getElementById('viewSiteBtn').addEventListener('click', () => {
        window.open('/', '_blank');
    });

    // Logout button
    document.getElementById('logoutBtn').addEventListener('click', () => {
        alert('Logout clicked!');
        // Tambah route logout Laravel bila dah setup Auth
    });

    // Mobile sidebar toggle
    const adminSidebar = document.getElementById('adminSidebar');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    hamburgerBtn.addEventListener('click', () => {
        adminSidebar.classList.toggle('open');
    });

    // Collapsible menu functionality
    const tablesMenu = document.getElementById('tablesMenu');
    const tablesSubmenu = document.getElementById('tablesSubmenu');
    
    // Check if submenu should be expanded on page load (if any order route is active)
    if (tablesMenu.classList.contains('active')) {
        tablesMenu.classList.add('expanded');
        tablesSubmenu.classList.add('expanded');
    }
    
    tablesMenu.addEventListener('click', () => {
        tablesMenu.classList.toggle('expanded');
        tablesSubmenu.classList.toggle('expanded');
    });
});
