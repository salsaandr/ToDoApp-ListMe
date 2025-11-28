import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'pink-kustom': '#eda3a1',
                'coklat': '#48342d',
                'putih-kustom': '#efebed',
                'pink-pucat': '#fee5d2',
                'krem': '#ffeede',
                'ungu-kustom': '#8c597c',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    safelist: [
        'bg-ungu-kustom',
        'dark:bg-ungu-kustom',
        'bg-pink-kustom',
        'dark:bg-pink-kustom',
        'text-ungu-kustom',
        'text-pink-kustom',
        'text-coklat',
        'bg-putih-kustom',
        'dark:bg-coklat',
        'bg-opacity-80', 
    ],

    plugins: [forms],
};