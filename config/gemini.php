<?php 

return [
    'key' => env('GEMINI_KEY'),
    'url' => env('GEMINI_URL', 'https://generativelanguage.googleapis.com/v1beta/models/'),
    'model' => env('GEMINI_MODEL', 'text-bison-001'),
];