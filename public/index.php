<?php

use App\Kernel;

    require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

    // Set the default timezone
    date_default_timezone_set('Europe/Paris');

    /**
     * Symfony Runtime Component entry point
     *
     * @param array $context[] Contains environment variables like APP_ENV and APP_DEBUG
     */
    return function (array $context) {
        // Ensure APP_ENV is a string (Default to 'prod' if missing)
        $env = (string) ($context['APP_ENV'] ?? 'prod');

        // Ensure APP_DEBUG is a boolean (Default to false if missing)
        $debug = (bool) ($context['APP_DEBUG'] ?? false);

        // Return the kernel with guaranteed types
    return new Kernel($env, $debug);
};
