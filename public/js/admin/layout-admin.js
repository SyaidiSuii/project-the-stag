document.addEventListener("DOMContentLoaded", () => {
    // 🌙 Animation contoh (fade in table rows) - with safety check
    const tableBody = document.querySelector("tbody");
    if (tableBody) {
        tableBody.querySelectorAll("tr").forEach((row, i) => {
            row.style.opacity = 0;
            setTimeout(() => {
                row.style.transition = "opacity 0.5s ease";
                row.style.opacity = 1;
            }, i * 150);
        });
    }

    // Set current date
    document.getElementById("currentDate").textContent =
        new Date().toLocaleDateString("en-MY", {
            weekday: "short",
            year: "numeric",
            month: "short",
            day: "numeric",
        });

    // View site button
    document.getElementById("viewSiteBtn").addEventListener("click", () => {
        window.open("/", "_blank");
    });

    // Logout button
    document.getElementById("logoutBtn").addEventListener("click", async () => {
        const confirmed = await showConfirm(
            'Logout?',
            'Are you sure you want to logout?',
            'warning',
            'Logout',
            'Cancel'
        );
        if (confirmed) {
            // Tambah route logout Laravel bila dah setup Auth
            Toast.info('Logging out...', 'Please wait');
        }
    });

    // Mobile sidebar toggle
    const adminSidebar = document.getElementById("adminSidebar");
    const hamburgerBtn = document.getElementById("hamburgerBtn");
    hamburgerBtn.addEventListener("click", () => {
        adminSidebar.classList.toggle("open");
    });

    

    // Menu Management Menu
    const menuMenu = document.getElementById("menuMenu");
    const menuSubmenu = document.getElementById("menuSubmenu");

    if (menuMenu && menuSubmenu) {
        // Check if submenu should be expanded on page load
        if (menuMenu.classList.contains("active")) {
            menuMenu.classList.add("expanded");
            menuSubmenu.classList.add("expanded");
        }

        menuMenu.addEventListener("click", () => {
            menuMenu.classList.toggle("expanded");
            menuSubmenu.classList.toggle("expanded");
        });
    }

    // Orders Menu
    const orderMenu = document.getElementById("orderMenu");
    const orderSubmenu = document.getElementById("orderSubmenu");

    if (orderMenu && orderSubmenu) {
        // Check if submenu should be expanded on page load
        if (orderMenu.classList.contains("active")) {
            orderMenu.classList.add("expanded");
            orderSubmenu.classList.add("expanded");
        }

        orderMenu.addEventListener("click", () => {
            orderMenu.classList.toggle("expanded");
            orderSubmenu.classList.toggle("expanded");
        });
    }
    
    // Bookings Menu
    const bookingsMenu = document.getElementById("bookingsMenu");
    const bookingsSubmenu = document.getElementById("bookingsSubmenu");

    if (bookingsMenu && bookingsSubmenu) {
        // Check if submenu should be expanded on page load
        if (bookingsMenu.classList.contains("active")) {
            bookingsMenu.classList.add("expanded");
            bookingsSubmenu.classList.add("expanded");
        }

        bookingsMenu.addEventListener("click", () => {
            bookingsMenu.classList.toggle("expanded");
            bookingsSubmenu.classList.toggle("expanded");
        });
    }

    // Rewards Menu
    const rewardsMenu = document.getElementById("rewardsMenu");
    const rewardsSubmenu = document.getElementById("rewardsSubmenu");

    if (rewardsMenu && rewardsSubmenu) {
        // Check if submenu should be expanded on page load
        if (rewardsMenu.classList.contains("active")) {
            rewardsMenu.classList.add("expanded");
            rewardsSubmenu.classList.add("expanded");
        }

        rewardsMenu.addEventListener("click", () => {
            rewardsMenu.classList.toggle("expanded");
            rewardsSubmenu.classList.toggle("expanded");
        });
    }

    // Stock Management Menu
    const stockMenu = document.getElementById("stockMenu");
    const stockSubmenu = document.getElementById("stockSubmenu");

    if (stockMenu && stockSubmenu) {
        // Check if submenu should be expanded on page load
        if (stockMenu.classList.contains("active")) {
            stockMenu.classList.add("expanded");
            stockSubmenu.classList.add("expanded");
        }

        stockMenu.addEventListener("click", () => {
            stockMenu.classList.toggle("expanded");
            stockSubmenu.classList.toggle("expanded");
        });
    }

    // Role & Permission Menu
    const roleMenu = document.getElementById("roleMenu");
    const roleSubmenu = document.getElementById("roleSubmenu");

    if (roleMenu && roleSubmenu) {
        if (roleMenu.classList.contains("active")) {
            roleMenu.classList.add("expanded");
            roleSubmenu.classList.add("expanded");
        }

        roleMenu.addEventListener("click", () => {
            roleMenu.classList.toggle("expanded");
            roleSubmenu.classList.toggle("expanded");
        });
    }
});
