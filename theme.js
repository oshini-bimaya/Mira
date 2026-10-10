
/* ==========================================
   MIRA - LIGHT & DARK THEME SWITCHER
   ========================================== */

(function () {
    const THEME_KEY = "mira-theme";

    // Read saved theme
    function getTheme() {
        try {
            return localStorage.getItem(THEME_KEY) || "light";
        } catch (error) {
            return "light";
        }
    }

    // Save selected theme
    function saveTheme(theme) {
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (error) {
            console.log("Theme preference could not be saved.");
        }
    }

    // Apply theme to website
    function applyTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);

        // Update all theme toggle buttons
        document.querySelectorAll(".theme-toggle").forEach(button => {
            const isDark = theme === "dark";

            button.textContent = isDark ? "☀️" : "🌙";
            button.setAttribute(
                "aria-label",
                isDark ? "Switch to light mode" : "Switch to dark mode"
            );
            button.setAttribute("title",
                isDark ? "Light Mode" : "Dark Mode"
            );
            button.setAttribute("aria-pressed", String(isDark));
        });
    }

    // Toggle between light and dark
    function toggleTheme() {
        const currentTheme =
            document.documentElement.getAttribute("data-theme");

        const newTheme =
            currentTheme === "dark" ? "light" : "dark";

        applyTheme(newTheme);
        saveTheme(newTheme);
    }

    // Apply theme immediately
    applyTheme(getTheme());

    // Initialize when page loads
    function initializeTheme() {
        // Find existing navigation
        const navbar = document.querySelector(
            ".user-nav-actions, .header-buttons"
        );

        // Create toggle button if one doesn't exist
        if (navbar && !navbar.querySelector(".theme-toggle")) {
            const button = document.createElement("button");

            button.type = "button";
            button.className = "theme-toggle";

            navbar.prepend(button);
        }

        // Add click events to toggle buttons
        document.querySelectorAll(".theme-toggle").forEach(button => {
            button.addEventListener("click", toggleTheme);
        });

        // Display correct icon
        applyTheme(getTheme());
    }

    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            initializeTheme
        );
    } else {
        initializeTheme();
    }
})();
