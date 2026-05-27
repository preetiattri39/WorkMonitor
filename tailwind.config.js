import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#eef8ff',
                    100: '#d9f0ff',
                    200: '#bce6ff',
                    300: '#8fd7ff',
                    400: '#5fc0ff',
                    500: '#349dff',
                    600: '#1f7ef4',
                    700: '#1d65e0',
                    800: '#214fb5',
                    900: '#22458e'
                }
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif']
            },
            boxShadow: {
                panel: '0 12px 40px rgba(15, 23, 42, 0.08)'
            }
        }
    },
    plugins: [forms]
};
