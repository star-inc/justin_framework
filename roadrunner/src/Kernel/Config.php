<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use Error;
use Exception;

class Config
{
    private const FILENAME = __DIR__ . "/../../config.inc.php";
    private const REQUIREMENTS = [
        "DB_DSN",
        "DB_USERNAME",
        "DB_PASSWORD"
    ];
    private array $config;

    public function __construct()
    {
        if (file_exists(self::FILENAME)) {
            $this->config = include self::FILENAME;
            foreach (self::REQUIREMENTS as $key) {
                if (!array_key_exists($key, $this->config)) {
                    throw new Error("$key required is not found in the configuration.");
                }
            }
        } else {
            throw new Error(sprintf("Configuration(%s) is not found.", self::FILENAME));
        }
    }

    public function get(string $key, bool $required = true): ?string
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        } elseif (!$required) {
            return null;
        } else {
            throw new Exception("$key is not found in the configuration");
        }
    }
}
