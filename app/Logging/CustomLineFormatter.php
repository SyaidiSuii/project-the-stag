<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Str;

class CustomLineFormatter extends LineFormatter
{
    /**
     * Overrides the default exception normalization to filter out vendor stack traces.
     *
     * @param \Throwable $e
     * @param int $depth
     * @return string
     */
    protected function normalizeException(\Throwable $e, int $depth = 0): string
    {
        // Get the default formatted exception string
        $str = parent::normalizeException($e, $depth);

        $lines = explode("\n", $str);
        $filteredLines = [];
        $isStackTrace = false;

        foreach ($lines as $line) {
            // The stack trace in Laravel logs starts after the "[stacktrace]" line
            if (Str::startsWith(trim($line), '[stacktrace]')) {
                $isStackTrace = true;
                $filteredLines[] = $line;
                continue;
            }

            // If we are in the stack trace section, filter lines containing the vendor path
            if ($isStackTrace) {
                if (Str::contains($line, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
                    continue;
                }
            }
            
            $filteredLines[] = $line;
        }

        // Re-add a note that vendor traces were hidden
        if ($isStackTrace) {
            $filteredLines[] = '#... (Vendor stack traces hidden)';
        }

        return implode("\n", $filteredLines);
    }
}
