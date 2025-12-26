// Simplified Sidebar Dropdown System
let sidebarOpen = true;

// Debug logging function
const DEBUG = false;
function debugLog(message) {
  if (DEBUG) {
    console.log("[Dashboard Debug]:", message);
  }
}

// Malaysian holidays for 2025
const malaysiHolidays = {
  "2025-01-01": "New Year's Day",
  "2025-01-29": "Chinese New Year",
  "2025-01-30": "Chinese New Year (Second Day)",
  "2025-02-01": "Federal Territory Day",
  "2025-03-31": "Hari Raya Nyepi (Balinese Day of Silence)",
  "2025-04-13": "Hari Raya Aidilfitri (Eid al-Fitr)",
  "2025-04-14": "Hari Raya Aidilfitri (Second Day)",
  "2025-05-01": "Labour Day",
  "2025-05-12": "Wesak Day (Buddha Day)",
  "2025-06-20": "Hari Raya Haji (Eid al-Adha)",
  "2025-07-10": "Awal Muharram (Islamic New Year)",
  "2025-08-31": "National Day (Merdeka Day)",
  "2025-09-07": "Prophet Muhammad's Birthday (Maulidur Rasul)",
  "2025-09-16": "Malaysia Day",
  "2025-11-01": "Deepavali (Diwali)",
  "2025-12-25": "Christmas Day",
};

// Utility functions
const dayNames = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];
const monthNames = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

function formatDate(date) {
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
}

function formatDateForHoliday(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

function updateDateTime() {
  const now = new Date();
  const malaysiaTime = new Date(
    now.toLocaleString("en-US", { timeZone: "Asia/Kuala_Lumpur" })
  );

  const timeString = now.toLocaleTimeString("en-MY", {
    timeZone: "Asia/Kuala_Lumpur",
    hour12: true,
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });

  const dateString = formatDate(malaysiaTime);
  const dayName = dayNames[malaysiaTime.getDay()];
  const currentDateKey = formatDateForHoliday(malaysiaTime);

  const subtitle = document.getElementById("dashboardSubtitle");
  if (subtitle) {
    subtitle.textContent = `Date : ${dateString} | Time : ${timeString}`;
  }

  const userGreeting = document.getElementById("userGreeting");
  if (userGreeting) {
    const holiday = malaysiHolidays[currentDateKey];
    if (holiday) {
      userGreeting.innerHTML = `Today : ${dayName} - ${holiday} 🎉`;
      const holidayInfo = document.getElementById("holidayInfo");
      const holidayName = document.getElementById("holidayName");
      if (holidayInfo && holidayName) {
        holidayName.textContent = holiday;
        holidayInfo.classList.add("show");
      }
    } else {
      userGreeting.textContent = `Today : ${dayName}`;
      const holidayInfo = document.getElementById("holidayInfo");
      if (holidayInfo) {
        holidayInfo.classList.remove("show");
      }
    }
  }
}

// User dropdown functions
function toggleUserMenu() {
  const menu = document.getElementById("userMenu");
  const dropdown = document.querySelector(".user-dropdown");

  if (menu && dropdown) {
    menu.classList.toggle("show");
    dropdown.classList.toggle("active");
  }
}

function handleLogout(event) {
  event.preventDefault();
  event.stopPropagation();
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "http://localhost/KoVoS/";
  }
  closeUserMenu();
}

function closeUserMenu() {
  const menu = document.getElementById("userMenu");
  const dropdown = document.querySelector(".user-dropdown");

  if (menu) menu.classList.remove("show");
  if (dropdown) dropdown.classList.remove("active");
}

// SIMPLIFIED SIDEBAR DROPDOWN SYSTEM
function toggleSidebarDropdown(dropdownId) {
  const dropdown = document.getElementById(dropdownId);
  const trigger = document.querySelector(
    `[data-dropdown="${dropdownId.replace("Dropdown", "")}"]`
  );

  if (!dropdown || !trigger) {
    debugLog(`Dropdown or trigger not found: ${dropdownId}`);
    return;
  }

  // Close all other dropdowns first
  document.querySelectorAll(".sidebar .dropdown").forEach((dd) => {
    if (dd.id !== dropdownId) {
      dd.classList.remove("show");
    }
  });

  // Remove dropdown-open class from all triggers
  document.querySelectorAll("[data-dropdown]").forEach((trig) => {
    if (trig !== trigger) {
      trig.classList.remove("dropdown-open");
    }
  });

  // Toggle current dropdown
  const isOpen = dropdown.classList.contains("show");

  if (isOpen) {
    dropdown.classList.remove("show");
    trigger.classList.remove("dropdown-open");
    debugLog(`Closed dropdown: ${dropdownId}`);
  } else {
    dropdown.classList.add("show");
    trigger.classList.add("dropdown-open");
    debugLog(`Opened dropdown: ${dropdownId}`);
  }
}

function closeAllSidebarDropdowns() {
  document.querySelectorAll(".sidebar .dropdown").forEach((dropdown) => {
    dropdown.classList.remove("show");
  });

  document.querySelectorAll("[data-dropdown]").forEach((trigger) => {
    trigger.classList.remove("dropdown-open");
  });

  debugLog("All sidebar dropdowns closed");
}

// Sidebar toggle function
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const hamburger = document.getElementById("hamburger");
  const overlay = document.getElementById("sidebarOverlay");
  const contentArea = document.querySelector(".content-area");

  sidebarOpen = !sidebarOpen;

  if (hamburger) {
    hamburger.classList.toggle("active");
  }

  if (window.innerWidth <= 768) {
    // Mobile behavior
    if (sidebarOpen) {
      if (sidebar) sidebar.classList.add("open");
      if (overlay) overlay.classList.add("active");
    } else {
      if (sidebar) sidebar.classList.remove("open");
      if (overlay) overlay.classList.remove("active");
    }
  } else {
    // Desktop behavior
    if (sidebarOpen) {
      if (sidebar) sidebar.classList.remove("hidden");
      if (contentArea) contentArea.classList.remove("expanded");
    } else {
      if (sidebar) sidebar.classList.add("hidden");
      if (contentArea) contentArea.classList.add("expanded");
    }
  }

  debugLog("Sidebar open: " + sidebarOpen);
}

// Window resize handler
window.addEventListener("resize", function () {
  const sidebar = document.getElementById("sidebar");
  const hamburger = document.getElementById("hamburger");
  const overlay = document.getElementById("sidebarOverlay");
  const contentArea = document.querySelector(".content-area");

  if (window.innerWidth > 768) {
    // Desktop mode
    if (sidebar) sidebar.classList.remove("open");
    if (overlay) overlay.classList.remove("active");

    if (sidebarOpen) {
      if (sidebar) sidebar.classList.remove("hidden");
      if (contentArea) contentArea.classList.remove("expanded");
    } else {
      if (sidebar) sidebar.classList.add("hidden");
      if (contentArea) contentArea.classList.add("expanded");
    }
  } else {
    // Mobile mode
    if (sidebar) sidebar.classList.remove("hidden");
    if (contentArea) contentArea.classList.remove("expanded");
    sidebarOpen = false;
    if (hamburger) hamburger.classList.remove("active");
    if (sidebar) sidebar.classList.remove("open");
    if (overlay) overlay.classList.remove("active");
  }
});

// Update dashboard content based on section
function updateDashboardContent(sectionName) {
  document.querySelectorAll(".content-section").forEach((section) => {
    section.classList.remove("active");
  });

  const targetSection = document.getElementById(sectionName);
  if (targetSection) {
    targetSection.classList.add("active");
  }

  const dashboardTitle = document.querySelector(".dashboard-title");

  switch (sectionName) {
    case "dashboard-section":
      if (dashboardTitle) dashboardTitle.textContent = "Dashboard";
      break;
    case "pendaftar-section":
      if (dashboardTitle)
        dashboardTitle.textContent = "Bilangan Pendaftar Kenderaan";
      break;
    case "ibu-bapa-section":
      if (dashboardTitle)
        dashboardTitle.textContent = "Pendaftar Kenderaan - Ibu Bapa";
      break;
    case "pelajar-section":
      if (dashboardTitle)
        dashboardTitle.textContent = "Pendaftar Kenderaan - Pelajar";
      break;
    case "staf-kakitangan-section":
      if (dashboardTitle)
        dashboardTitle.textContent = "Pendaftar Kenderaan - Staf/Kakitangan";
      break;
    case "pelawat-section":
      if (dashboardTitle) dashboardTitle.textContent = "Bilangan Pelawat";
      break;
    case "other-section":
      if (dashboardTitle) dashboardTitle.textContent = "Lain-lain";
      break;
    default:
      if (dashboardTitle) dashboardTitle.textContent = "Dashboard";
  }
}

// SIMPLIFIED EVENT SETUP
function setupSidebarEvents() {
  debugLog("Setting up simplified sidebar events...");

  // Handle dropdown triggers
  document.querySelectorAll("[data-dropdown]").forEach((trigger) => {
    const dropdownName = trigger.getAttribute("data-dropdown");
    const dropdownId = dropdownName + "Dropdown";

    debugLog(`Setting up dropdown trigger: ${dropdownName} -> ${dropdownId}`);

    trigger.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleSidebarDropdown(dropdownId);
    });
  });

  // Handle dropdown menu items
  document.querySelectorAll(".dropdown .sidebar-item").forEach((item) => {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      debugLog(`Dropdown item clicked: ${this.textContent.trim()}`);

      // Remove active class from all sidebar items
      document.querySelectorAll(".sidebar-item").forEach((i) => {
        i.classList.remove("active");
      });

      // Add active class to clicked item
      this.classList.add("active");

      // Get section name and update content
      const sectionName = this.getAttribute("data-section");
      if (sectionName) {
        updateDashboardContent(sectionName);
      }

      // Close all dropdowns
      closeAllSidebarDropdowns();

      // Close sidebar on mobile
      if (window.innerWidth <= 768 && sidebarOpen) {
        toggleSidebar();
      }
    });
  });

  // Handle regular sidebar items (non-dropdown)
  document
    .querySelectorAll(
      ".sidebar-item:not(.dropdown .sidebar-item):not([data-dropdown])"
    )
    .forEach((item) => {
      item.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        debugLog(`Regular sidebar item clicked: ${this.textContent.trim()}`);

        // Remove active class from all sidebar items
        document.querySelectorAll(".sidebar-item").forEach((i) => {
          i.classList.remove("active");
        });

        // Add active class to clicked item
        this.classList.add("active");

        // Get section name and update content
        const sectionName = this.getAttribute("data-section");
        if (sectionName) {
          updateDashboardContent(sectionName);
        }

        // Close all dropdowns
        closeAllSidebarDropdowns();

        // Close sidebar on mobile
        if (window.innerWidth <= 768 && sidebarOpen) {
          toggleSidebar();
        }
      });
    });
}

// Handle clicks outside to close dropdowns
function setupClickOutside() {
  document.addEventListener("click", function (event) {
    const userDropdown = document.querySelector(".user-dropdown");

    // Close user menu when clicking outside
    if (userDropdown && !userDropdown.contains(event.target)) {
      closeUserMenu();
    }

    // Close sidebar dropdowns when clicking outside sidebar
    if (!event.target.closest(".sidebar")) {
      closeAllSidebarDropdowns();
    }
  });
}

// Handle sidebar overlay clicks
function setupSidebarOverlay() {
  const sidebarOverlay = document.getElementById("sidebarOverlay");
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", function () {
      if (window.innerWidth <= 768 && sidebarOpen) {
        toggleSidebar();
      }
    });
  }
}

// Handle keyboard events
function setupKeyboardEvents() {
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeUserMenu();
      closeAllSidebarDropdowns();

      if (window.innerWidth <= 768 && sidebarOpen) {
        toggleSidebar();
      }
    }
  });
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  debugLog("DOM Content Loaded - Initializing simplified dashboard...");

  // Setup all event handlers
  setupSidebarEvents();
  setupClickOutside();
  setupSidebarOverlay();
  setupKeyboardEvents();

  // Initialize sidebar state for mobile
  if (window.innerWidth <= 768) {
    sidebarOpen = false;
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (hamburger) hamburger.classList.remove("active");
    if (sidebar) sidebar.classList.remove("open");
    if (overlay) overlay.classList.remove("active");
  }

  // Initialize content area state on page load
  const contentArea = document.querySelector(".content-area");
  if (window.innerWidth > 768) {
    if (!sidebarOpen) {
      if (contentArea) contentArea.classList.add("expanded");
    }
  }

  // Initial update
  updateDateTime();
  debugLog("Simplified dashboard initialization complete");
});

// Update date/time every second
setInterval(updateDateTime, 1000);

// Initial call
updateDateTime();
