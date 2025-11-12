<?php

declare(strict_types=1);

/**
 * Minimal .env loader compatible with simple KEY=VALUE pairs.
 * - Supports quoted values
 * - Ignores empty lines and lines starting with '#'
 * - Whitespace around keys and values is trimmed
 */
function loadEnv(string $envFilePath): void
{
    if (!is_file($envFilePath) || !is_readable($envFilePath)) {
        return; // Silently ignore if file missing; caller can handle defaults
    }

    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $rawLine) {
        $line = trim($rawLine);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $equalsPos = strpos($line, '=');
        if ($equalsPos === false) {
            continue; // Not a key=value line
        }

        $key = trim(substr($line, 0, $equalsPos));
        $value = trim(substr($line, $equalsPos + 1));

        if ($value === '') {
            // Empty value
        } elseif ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        // Normalize Windows-style escaped quotes if present
        $value = str_replace(['\"', "\'"], ['"', "'"], $value);

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

/**
 * Helper to read env with default.
 */
function env(string $key, ?string $default = null): ?string
{
    $val = getenv($key);
    if ($val === false) {
        return $default;
    }
    return $val;
} 