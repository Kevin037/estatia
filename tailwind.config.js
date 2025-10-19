import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#ECFDF5',
                    100: '#D1FAE5',
                    200: '#A7F3D0',
                    300: '#6EE7B7',
                    400: '#34D399',
                    500: '#10B981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065F46',
                    900: '#064E3B',
                    950: '#022C22',
                },
            },
        },
    },

    plugins: [
        forms,
        function({ addUtilities }) {
            const newUtilities = {
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                },
                '.scrollbar-thumb-emerald-700': {
                    '--scrollbar-thumb': '#047857',
                },
                '.scrollbar-track-emerald-900': {
                    '--scrollbar-track': '#064E3B',
                },
                '.scrollbar-thin::-webkit-scrollbar': {
                    width: '8px',
                    height: '8px',
                },
                '.scrollbar-thin::-webkit-scrollbar-track': {
                    background: 'var(--scrollbar-track, #064E3B)',
                },
                '.scrollbar-thin::-webkit-scrollbar-thumb': {
                    background: 'var(--scrollbar-thumb, #047857)',
                    'border-radius': '4px',
                },
                '.scrollbar-thin::-webkit-scrollbar-thumb:hover': {
                    background: '#059669',
                },
            };
            addUtilities(newUtilities);
        },
    ],
};

