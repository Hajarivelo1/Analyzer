const mix = require('laravel-mix');

// Configuration principale
mix.js('resources/js/admin_master.js', 'public/js')
   .css('resources/css/admin_master.css', 'public/css')
   .options({
        processCssUrls: false, // Désactive le traitement des URLs dans CSS
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true, // Supprime les console.log en production
                },
            },
        },
    })
   .version() // Ajoute le versioning pour le cache busting
   .sourceMaps(); // Optionnel : pour le debug en développement

// Optimisations supplémentaires
if (mix.inProduction()) {
    mix.minify('public/js/admin_master.js')
       .minify('public/css/admin_master.css');
}
