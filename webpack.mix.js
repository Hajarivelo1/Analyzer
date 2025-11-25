const mix = require('laravel-mix'); 
 
/* 
*/ 
 
// ?? VOS ASSETS SEO ANALYZER 
mix.js('resources/js/seo-analyzer.js', 'public/js') 
   .css('resources/css/seo-analyzer.css', 'public/css') 
   .version(); // ?? Active le versioning automatique 
 
// Option: Source maps en d‚veloppement  
if (!mix.inProduction()) { 
    mix.sourceMaps(); 
} 
