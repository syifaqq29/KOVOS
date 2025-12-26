<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Dropdown Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            padding: 80px 0 20px 0;
            /* Added top padding to push content down */
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-right: 4px solid #fff;
        }

        .sidebar-icon {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }

        .dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
        }

        .dropdown.show {
            max-height: 300px;
        }

        .dropdown .sidebar-item {
            padding-left: 60px;
            font-size: 14px;
        }

        .dropdown .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-item[data-dropdown]:hover .arrow,
        .sidebar-item[data-dropdown].dropdown-open .arrow {
            transform: rotate(180deg);
        }

        .debug {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <nav class="sidebar" id="sidebar">
        <a href="#" class="sidebar-item active" data-section="dashboard-section">
            <div class="sidebar-icon"><i class="fa-solid fa-gauge"></i></div>
            <span class="text">Dashboard</span>
        </a>

        <a href="#" class="sidebar-item" data-dropdown="category">
            <div class="sidebar-icon"><i class="fa-solid fa-users"></i></div>
            <span>Select Category</span>
            <span class="arrow">▼</span>
        </a>

        <div class="dropdown" id="categoryDropdown">
            <a href="#" class="sidebar-item" data-section="ibu-bapa-section">
                <div class="sidebar-icon"><i class="fa-solid fa-user-tie"></i></div>
                <span>Parents</span>
            </a>
            <a href="#" class="sidebar-item" data-section="pelajar-section">
                <div class="sidebar-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <span>Students</span>
            </a>
            <a href="#" class="sidebar-item" data-section="staf-kakitangan-section">
                <div class="sidebar-icon"><i class="fa-solid fa-user-gear"></i></div>
                <span>Staff</span>
            </a>
        </div>

        <a href="#" class="sidebar-item" data-section="pelawat-section">
            <div class="sidebar-icon"><i class="fa-solid fa-eye"></i></div>
            <span>Visitor</span>
        </a>
    </nav>

    <script>
        function debugLog(message) {
            const debugElement = document.getElementById('debugText');
            if (debugElement) {
                debugElement.innerHTML += '<br>' + message;
            }
            console.log(message);
        }

        function showDropdown(dropdownElement) {
            if (dropdownElement) {
                debugLog('Showing dropdown: ' + dropdownElement.id);
                // Close other dropdowns first
                document.querySelectorAll(".dropdown").forEach((dropdown) => {
                    if (dropdown !== dropdownElement) {
                        dropdown.classList.remove("show");
                    }
                });
                dropdownElement.classList.add("show");
            }
        }

        function hideDropdown(dropdownElement) {
            if (dropdownElement) {
                debugLog('Hiding dropdown: ' + dropdownElement.id);
                dropdownElement.classList.remove("show");
            }
        }

        function toggleDropdown(dropdownElement) {
            if (dropdownElement) {
                debugLog('Toggling dropdown: ' + dropdownElement.id);
                if (dropdownElement.classList.contains("show")) {
                    hideDropdown(dropdownElement);
                } else {
                    showDropdown(dropdownElement);
                }
            }
        }

        // Setup dropdown events
        function setupDropdownEvents() {
            debugLog('Setting up dropdown events...');

            // Handle dropdown trigger items (items that have data-dropdown attribute)
            document.querySelectorAll("[data-dropdown]").forEach((item, index) => {
                const dropdownName = item.getAttribute("data-dropdown");
                const dropdownId = dropdownName + "Dropdown";
                const dropdownElement = document.getElementById(dropdownId);

                debugLog(`Found dropdown trigger ${index}: ${dropdownName} -> ${dropdownId}`);
                debugLog(`Dropdown element found: ${dropdownElement ? 'YES' : 'NO'}`);

                if (dropdownElement) {
                    let hoverTimeout;

                    // Click to toggle
                    item.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debugLog('Dropdown clicked: ' + dropdownName);

                        // Toggle dropdown-open class for arrow animation
                        item.classList.toggle('dropdown-open');

                        toggleDropdown(dropdownElement);
                    });

                    // Hover events
                    item.addEventListener("mouseenter", function () {
                        clearTimeout(hoverTimeout);
                        debugLog('Mouse enter dropdown trigger: ' + dropdownName);
                        showDropdown(dropdownElement);
                        item.classList.add('dropdown-open');
                    });

                    item.addEventListener("mouseleave", function () {
                        hoverTimeout = setTimeout(() => {
                            debugLog('Mouse leave dropdown trigger: ' + dropdownName);
                            hideDropdown(dropdownElement);
                            item.classList.remove('dropdown-open');
                        }, 200);
                    });

                    // Keep dropdown open when hovering over it
                    dropdownElement.addEventListener("mouseenter", function () {
                        clearTimeout(hoverTimeout);
                        debugLog('Mouse enter dropdown menu: ' + dropdownName);
                    });

                    dropdownElement.addEventListener("mouseleave", function () {
                        hoverTimeout = setTimeout(() => {
                            debugLog('Mouse leave dropdown menu: ' + dropdownName);
                            hideDropdown(dropdownElement);
                            item.classList.remove('dropdown-open');
                        }, 200);
                    });
                } else {
                    debugLog('ERROR: Dropdown element not found for: ' + dropdownId);
                }
            });
        }

        // Handle dropdown menu items
        function setupDropdownItemEvents() {
            document.querySelectorAll(".dropdown .sidebar-item").forEach((item) => {
                item.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    debugLog('Dropdown item clicked: ' + this.textContent.trim());

                    // Remove active class from all sidebar items
                    document.querySelectorAll(".sidebar-item").forEach((i) => {
                        i.classList.remove("active");
                    });

                    // Add active class to clicked dropdown item
                    this.classList.add("active");

                    // Close dropdown after selection
                    const dropdownMenu = this.closest(".dropdown");
                    if (dropdownMenu) {
                        dropdownMenu.classList.remove("show");
                        // Remove dropdown-open from trigger
                        document.querySelectorAll("[data-dropdown]").forEach(trigger => {
                            trigger.classList.remove('dropdown-open');
                        });
                    }
                });
            });
        }

        // Handle regular sidebar items
        function setupRegularSidebarItems() {
            document.querySelectorAll(".sidebar-item:not(.dropdown .sidebar-item):not([data-dropdown])").forEach((item) => {
                item.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    debugLog('Regular sidebar item clicked: ' + this.textContent.trim());

                    // Remove active class from all sidebar items
                    document.querySelectorAll(".sidebar-item").forEach((i) => {
                        i.classList.remove("active");
                    });

                    // Add active class to clicked item
                    this.classList.add("active");
                });
            });
        }

        // Close dropdowns when clicking outside
        function setupClickOutside() {
            document.addEventListener("click", function (event) {
                if (!event.target.closest(".dropdown") && !event.target.closest("[data-dropdown]")) {
                    debugLog('Click outside detected');
                    document.querySelectorAll(".sidebar .dropdown").forEach((dropdown) => {
                        dropdown.classList.remove("show");
                    });
                    document.querySelectorAll("[data-dropdown]").forEach(trigger => {
                        trigger.classList.remove('dropdown-open');
                    });
                }
            });
        }

        // Initialize everything
        document.addEventListener("DOMContentLoaded", function () {
            debugLog('DOM Content Loaded');
            setupDropdownEvents();
            setupDropdownItemEvents();
            setupRegularSidebarItems();
            setupClickOutside();
            debugLog('All events setup complete');
        });
    </script>
</body>

</html>