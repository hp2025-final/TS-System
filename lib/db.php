<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * Returns a connected mysqli instance using env variables.
 * Expected env keys:
 * - DB_HOST
 * - DB_PORT (optional, default 3306)
 * - DB_DATABASE
 * - DB_USERNAME
 * - DB_PASSWORD
 */
function db(): mysqli
{
    // Load .env from project root if not already loaded
    $projectRoot = realpath(__DIR__ . '/..');
    if ($projectRoot !== false) {
        loadEnv($projectRoot . DIRECTORY_SEPARATOR . '.env');
    }

    $host = env('DB_HOST', '127.0.0.1');
    $port = (int) (env('DB_PORT', '3306'));
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');

    if ($database === null || $username === null || $password === null) {
        http_response_code(500);
        throw new RuntimeException('Database env variables missing. Expected DB_DATABASE, DB_USERNAME, DB_PASSWORD.');
    }

    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('Failed to initialize mysqli.');
    }

    // Better timeouts for CLI/web
    $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

    if (!$mysqli->real_connect($host, $username, $password, $database, $port)) {
        throw new RuntimeException('Database connection failed: ' . mysqli_connect_error());
    }

    // Use strict SQL mode for safety
    $mysqli->query("SET SESSION sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

    return $mysqli;
} 