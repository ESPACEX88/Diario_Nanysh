import { ref, onMounted } from 'vue';

const isDark = ref(false);

export const useDarkMode = () => {
    const initDarkMode = () => {
        // Check localStorage first
        const saved = localStorage.getItem('darkMode');
        if (saved !== null) {
            isDark.value = saved === 'true';
        } else {
            // Check system preference
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        applyDarkMode();
    };

    const applyDarkMode = () => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('darkMode', String(isDark.value));
    };

    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        applyDarkMode();
    };

    // Initialize on import
    if (typeof window !== 'undefined') {
        initDarkMode();
    }

    return {
        isDark,
        toggleDarkMode,
    };
};

