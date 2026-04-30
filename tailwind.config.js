import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                amiri: ['Amiri', 'serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#2596be',
                    hover: '#31a7d1',
                    active: '#1c7ca0',
                }
            }
        },
    },

    plugins: [forms],
};
