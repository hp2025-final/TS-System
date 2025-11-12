<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Execute all statements in a .sql file using multi_query.
 * Returns an array with keys: ok (bool), messages (string[]), error (string|null).
 */
function runSqlFile(mysqli $connection, string $sqlFilePath): array
{
    $result = [
        'ok' => false,
        'messages' => [],
        'error' => null,
    ];

    $absolutePath = realpath($sqlFilePath) ?: $sqlFilePath;

    if (!is_file($absolutePath) || !is_readable($absolutePath)) {
        $result['error'] = 'SQL file not found or not readable: ' . $sqlFilePath;
        return $result;
    }

    $sql = file_get_contents($absolutePath);
    if ($sql === false) {
        $result['error'] = 'Failed to read SQL file: ' . $sqlFilePath;
        return $result;
    }

    if (trim($sql) === '') {
        $result['ok'] = true;
        $result['messages'][] = 'SQL file is empty; nothing to execute.';
        return $result;
    }

    // Execute all statements
    if (!$connection->multi_query($sql)) {
        $result['error'] = 'Execution failed: ' . $connection->error;
        return $result;
    }

    // Iterate through all results to surface mid-stream errors
    do {
        if ($store = $connection->store_result()) {
            // Free any result sets (e.g., SELECT used in verification)
            $store->free();
        }
        if ($connection->errno) {
            $result['error'] = 'Execution error: ' . $connection->error;
            return $result;
        }
    } while ($connection->more_results() && $connection->next_result());

    if ($connection->errno) {
        $result['error'] = 'Execution error: ' . $connection->error;
        return $result;
    }

    $result['ok'] = true;
    $result['messages'][] = 'Executed successfully: ' . basename($absolutePath);
    return $result;
} 