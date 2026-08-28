<script>
    (function() {
        var defaultThemeMode = "light";
        var themeMode = defaultThemeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                var stored = localStorage.getItem("data-bs-theme") || localStorage.getItem("data-bs-theme-mode") || localStorage.getItem("kt_theme_mode_value");
                if (stored !== null && stored !== "") {
                    themeMode = stored;
                } else {
                    themeMode = defaultThemeMode;
                }
            }

            var resolvedTheme = themeMode;
            if (themeMode === "system") {
                resolvedTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", resolvedTheme);
            document.documentElement.setAttribute("data-bs-theme-mode", themeMode);
        }
    })();
</script>
