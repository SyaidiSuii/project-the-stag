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
});
