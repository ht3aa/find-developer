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
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
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
