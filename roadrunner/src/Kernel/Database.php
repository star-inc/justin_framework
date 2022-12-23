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
