/**
 * Dark Mode Toggle Functionality
 * Toggles dark mode by adding/removing 'dark' class on html element
 */

(function () {
    'use strict';

    // Get the theme from localStorage or default to light
    function getTheme() {
        return localStorage.getItem('theme') || 'light';
    }

    // Set the theme
    function setTheme(theme) {
        // Kill all transitions so the switch is instant
        document.documentElement.classList.add('no-transitions');

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);

        // Re-enable transitions after repaint
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                document.documentElement.classList.remove('no-transitions');
            });
        });
    }

    // Initialize theme on page load
    function initTheme() {
        const theme = getTheme();
        setTheme(theme);
    }

    // Toggle theme
    function toggleTheme() {
        const currentTheme = getTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        return newTheme;
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }

    // Expose toggle function globally
    window.toggleDarkMode = toggleTheme;
    window.getDarkMode = getTheme;
    window.setDarkMode = setTheme;
})();
