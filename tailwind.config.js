import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './modules/**/resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                forest: {
                    50:  '#f0f5f1',
                    100: '#dce8de',
                    200: '#b9d1bf',
                    300: '#8fb89a',
                    400: '#6a9e78',
                    500: '#4d8460',
                    600: '#386650',
                    700: '#2d5241',
                    800: '#254234',
                    900: '#1e362b',
                    950: '#101e18',
                },
                beige: {
                    50:  '#faf9f7',
                    100: '#f4f3f0',
                    200: '#e8e6e1',
                    300: '#d5d2cb',
                },
            },
        },
    },

    plugins: [forms],
};
