document.addEventListener("DOMContentLoaded", () => {
    const themeToggleBtn = document.getElementById("theme-toggle");
    const currentTheme = localStorage.getItem("theme");

    // Apply the saved theme on load
    if (currentTheme) {
        document.body.classList.add(currentTheme);
        updateIcon(currentTheme);
    } else {
        // Check OS preference
        const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
        if (prefersDark) {
            document.body.classList.add("dark-theme");
            updateIcon("dark-theme");
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener("click", () => {
            if (document.body.classList.contains("dark-theme")) {
                document.body.classList.remove("dark-theme");
                localStorage.setItem("theme", "light-theme");
                updateIcon("light-theme");
            } else {
                document.body.classList.add("dark-theme");
                localStorage.setItem("theme", "dark-theme");
                updateIcon("dark-theme");
            }
        });
    }

    function updateIcon(theme) {
        if (!themeToggleBtn) return;
        if (theme === "dark-theme") {
            themeToggleBtn.innerHTML = "☀️"; // Show sun to toggle light mode
        } else {
            themeToggleBtn.innerHTML = "🌙"; // Show moon to toggle dark mode
        }
    }
});
