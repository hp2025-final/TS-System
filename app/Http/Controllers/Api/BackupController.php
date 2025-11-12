<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    public function download(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->email !== 'admin@tspos.com') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $connection = config('database.connections.mysql');
        $host = $connection['host'] ?? '127.0.0.1';
        $port = $connection['port'] ?? '3306';
        $database = $connection['database'] ?? '';
        $username = $connection['username'] ?? '';
        $password = $connection['password'] ?? '';

        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "TS_{$timestamp}.sql";

        // Possible mysqldump executables to try (Windows XAMPP first)
        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'mysqldump'
        ];

        $mysqldump = null;
        foreach ($candidates as $bin) {
            // On Windows, file_exists works for absolute path, for PATH commands we just try to use them later
            if (file_exists($bin) || $bin === 'mysqldump') {
                $mysqldump = $bin;
                break;
            }
        }

        if (!$mysqldump) {
            return response()->json(['message' => 'mysqldump not found on server'], 500);
        }

        $tmpPath = storage_path('app/'.$fileName);

        // Build command safely
        $cmd = '"' . $mysqldump . '"'
            . ' --host=' . escapeshellarg($host)
            . ' --port=' . escapeshellarg($port)
            . ' --user=' . escapeshellarg($username)
            . ' --password=' . escapeshellarg($password)
            . ' --routines --events --single-transaction '
            . escapeshellarg($database)
            . ' > ' . escapeshellarg($tmpPath);

        // Execute command
        $resultCode = null;
        if (str_starts_with(PHP_OS, 'WIN')) {
            // Use shell on Windows
            @exec($cmd, $output, $resultCode);
        } else {
            @exec($cmd . ' 2>&1', $output, $resultCode);
        }

        if ($resultCode !== 0 || !file_exists($tmpPath) || filesize($tmpPath) === 0) {
            // Clean up empty file if created
            if (file_exists($tmpPath)) @unlink($tmpPath);
            return response()->json([
                'message' => 'Backup failed',
                'code' => $resultCode,
            ], 500);
        }

        // Stream download and delete after send
        return response()->download($tmpPath, $fileName, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }
}
