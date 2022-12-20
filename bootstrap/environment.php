<?php

/*
  |--------------------------------------------------------------------------
  | Detect The Application Environment
  |--------------------------------------------------------------------------
  |
  | Laravel takes a dead simple approach to your application environments
  | so you can just specify a machine name for the host that matches a
  | given environment, then we will automatically detect it for you.
  |
  | Added to Base Laravel Install
 */

$env = $app->detectEnvironment(function () {
    // Default to using the 'dev' environment.
    $env = getenv('APP_ENV') ?? 'dev';
    $path_to_env = realpath(__DIR__ . '/../');

    // Support .env as a pointer to the current environment.
    if (file_exists($path_to_env . "/.env")) {
        $env = trim(file_get_contents($path_to_env . "/.env"));
    }
    putenv('APP_ENV=' . $env);

    if ($env !== 'testing') {
        // DMagalnick - 2022-09-06 - Note that in this version of Dotenv, there is no overload,
        // so the load must happen in reverse order so that the first occurrence of the environmental sticks.
        // I'm also pulling the .env.secrets into the if !testing, which will put it on the test environment
        // to have its own fully functional environment set up.
        // I'm also pulling the if file exists and putenv calls out of the if, as those should happen no matter what.

        // Look for and apply an .env.secrets file
        if (file_exists($path_to_env . "/.env.secrets")) {
            // $dotenv = new Dotenv\Dotenv($path_to_env, '/.env.secrets');
            // $dotenv->overload(); //this is important
            $dotenv = Dotenv\Dotenv::createImmutable($path_to_env, '/.env.secrets');
            $dotenv->load(); //this is important
        }

        // Look for and apply an override file
        if (file_exists($path_to_env . "/.env.override")) {
            // $dotenv = new Dotenv\Dotenv($path_to_env, '/.env.override');
            // $dotenv->overload(); //this is important
            $dotenv = Dotenv\Dotenv::createImmutable($path_to_env, '/.env.override');
            $dotenv->load(); //this is important
        }
    }

    // The environment named in the main .env file should always run, since it might be 'testing'
    if (file_exists($path_to_env . "/.env." . $env)) {
        // $dotenv = new Dotenv\Dotenv($path_to_env, '/.env.' . $env);
        // $dotenv->overload(); //this is important
        $dotenv = Dotenv\Dotenv::createImmutable($path_to_env, '/.env.' . $env);
        $dotenv->load(); //this is important
    }

});
