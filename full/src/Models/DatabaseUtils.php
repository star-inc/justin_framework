<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Models;

use PDO;
use PDOStatement;
use JustinExample\Kernel\Database;
use JustinExample\Kernel\DuplicateResultException;

trait DatabaseUtils
{
    public function simpleGrabOne(Database $db_instance, string $sql, array $params): ModelInterface
    {
        $stmt = $db_instance->getClient()->prepare($sql);
        $stmt->execute($params);
        $this->loadResult($stmt);
        return $this;
    }

    public function simpleGrabAll(Database $db_instance, string $sql, array $params = null): array
    {
        $stmt = $db_instance->getClient()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function (array $item) {
            $data = new static();
            $data->fromArray($item);
            return $data;
        }, $result);
    }

    public function simpleModifyFilled(Database $db_instance, string $sql): bool
    {
        $stmt = $db_instance->getClient()->prepare($sql);
        $db_instance->bindParamsFilled($stmt, $this->toArray());
        return $stmt->execute();
    }

    public function simpleModifySafe(Database $db_instance, string $sql, array $fields): bool
    {
        $stmt = $db_instance->getClient()->prepare($sql);
        $db_instance->bindParamsSafe($stmt, $this->toArray(), $fields);
        return $stmt->execute();
    }

    public function loadResult(PDOStatement $stmt): ModelInterface
    {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 1) {
            throw new DuplicateResultException();
        }
        if (count($result) === 1) {
            $this->fromArray($result[0]);
        }
        return $this;
    }
}
