<?php

namespace src\support;

class Enviroment
{
    /**
     * @param string $sir
     * @return void
     */
    public static function load(string $dir): void
    {
        if (!file_exists("{$dir}/.env") && !is_file("{$dir}/.env")) {
            return;
        }
        $lines = file("{$dir}/.env");
        foreach ($lines as $line) {
            if ($line !== PHP_EOL) {
                putenv(trim($line));
            }
        }
    }
}
