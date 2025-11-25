import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',           // Ajoutez cette ligne
                'resources/js/app.js',             // Ajoutez cette ligne
                'resources/css/admin_master.css',  // Gardez celle-ci
                'resources/js/admin_master.js' ,
                'resources/js/show.js' // 🔥 AJOUTEZ CETTE LIGNE    // Gardez celle-ci
            ],
            refresh: true,
        }),
    ],
});
