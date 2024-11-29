<?php

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($className, $methodName, $retries = 1, $priority = 'NORMAL', $delay = 0, ...$params): bool
    {
        // Prepare the command to execute the Artisan command
        $command = "php " . base_path('artisan') . " app:runBgJob \"$className\" \"$methodName\" "
            . implode(' ', array_map('escapeshellarg', $params)). " --retries=" . escapeshellarg($retries)
            . " --priority=" . escapeshellarg($priority). " --delay=" . escapeshellarg($delay);

        // Check the operating system
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Windows
            $status = pclose(popen("start /b " . $command, "r")); // Run the command in the background
            return $status == -1;
        } else {
            // Unix-based (Linux, macOS)
            $status = exec($command . " > /dev/null 2>&1 &"); // Run the command in the background
            return $status == -1;
        }

        return false; // Indicate success
    }
}
