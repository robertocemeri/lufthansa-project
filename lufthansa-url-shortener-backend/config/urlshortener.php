<?php
// config/urlshortener.php

return [
    'default_expiry_time' => env('URL_SHORTENER_EXPIRY_TIME', 5), // Default to 60 minutes if not set in .env
];
