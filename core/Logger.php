<?php

namespace core;

class Logger
{
    private static string $logFile = 'app.log';

    public static function log($message, $level = 'INFO'): void
    {
        try {
            $dir = __DIR__ . '/../storage/logs';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }

            $logPath = $dir . '/' . date('Y-m-d') . '_' . self::$logFile;
            $timestamp = date('Y-m-d H:i:s');
            $formattedMessage = "[$timestamp] [$level]: " . (is_string($message) ? $message : print_r($message, true)) . PHP_EOL;

            @file_put_contents($logPath, $formattedMessage, FILE_APPEND);
        } catch (\Throwable $e) {
            // Last resort
            @error_log("Logger failed: " . $e->getMessage());
        }
    }

    public static function error($message): void
    {
        self::log($message, 'ERROR');
    }

    public static function warning($message): void
    {
        self::log($message, 'WARNING');
    }

    public static function info($message): void
    {
        self::log($message, 'INFO');
    }
}
