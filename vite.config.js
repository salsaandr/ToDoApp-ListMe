// vite.config.js

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Entry Point Utama (Global)
                'resources/css/app.css', 
                'resources/js/app.js', 

                // Entry Point Khusus Halaman
                'resources/css/pages/welcome.css', // CSS untuk Landing Page
                'resources/css/pages/auth/login.css', // CSS untuk Login
                'resources/css/pages/auth/register.css', // CSS untuk Register
            ],
            refresh: true,
        }),
    ],
});