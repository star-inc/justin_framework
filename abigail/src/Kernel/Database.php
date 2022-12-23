<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use PDO;

class Database
{
    private PDO $client;

    public function __construct(Config $config)
    {
        $this->client = new PDO(
            $config->get("DB_DSN"),
            $config->get("DB_USERNAME"),
            $config->get("DB_PASSWORD"),
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]
        );
    }

    // https://www.uuidgenerator.net/dev-corner/php
    public static function guidV4($data = null): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function bindParamsFilled(object $stmt, array $data)
    {
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
    }

    public static function bindParamsSafe(object $stmt, array $data, array $fields)
    {
        foreach ($fields as $key) {
            $stmt->bindParam(":$key", $data[$key]);
        }
    }

    public function getClient(): PDO
    {
        return $this->client;
    }
}
