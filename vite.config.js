import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/admin_master.css',
                'resources/js/admin_master.js'
            ],
            refresh: true,
        }),
    ],
});
