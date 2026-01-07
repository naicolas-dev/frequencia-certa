<?php 

return [
    'key' => env('GEMINI_API_KEY'),
    'url' => env('GEMINI_URL', 'https://generativelanguage.googleapis.com/v1beta/models/'),
    'model' => env('GEMINI_MODEL'),
];