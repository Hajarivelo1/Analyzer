<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration IA / Ollama
    |--------------------------------------------------------------------------
    |
    | Ce fichier définit les paramètres pour ton intégration avec Ollama Cloud
    | ou une instance locale. Les valeurs sont chargées depuis .env, avec
    | des valeurs par défaut pour éviter les erreurs si .env est incomplet.
    |
    */

    'ollama' => [
        // Activer/désactiver le service
        'enabled'  => env('OLLAMA_ENABLED', true),

        // Endpoint API (par défaut instance locale Ollama)
        'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434/api/chat'),

        // Clé API (si nécessaire, sinon vide)
        'key'      => env('OLLAMA_API_KEY', ''),

        // Modèle utilisé
        'model'    => env('OLLAMA_MODEL', 'gpt-oss:120b-cloud'),

        // Timeout en secondes (toujours un entier)
        'timeout'  => (int) env('OLLAMA_TIMEOUT_SECONDS', 30),
    ],

];
