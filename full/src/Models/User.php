<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Models;

use JustinExample\Kernel\Database;
use TypeError;

class User extends ModelBase implements ModelInterface
{
    public string $uuid;
    public string $username;
    public string $password;
    public int $created_time;

    use DatabaseUtils;

    public function checkReady(): bool
    {
        return isset($this->uuid);
    }

    public function batch(Database $db_instance): array
    {
        $sql = "SELECT `uuid`, `username`, `password`, `emp_name` as `display_name`, `created_time` FROM `users` LEFT JOIN `employees` ON `users`.`uuid` = `employees`.`emp_id`";
        return $this->simpleGrabAll($db_instance, $sql);
    }

    public function load(Database $db_instance, $filter): ModelInterface
    {
        if (!is_string($filter)) {
            throw new TypeError();
        }
        $sql = "SELECT `uuid`, `username`, `password`, `emp_name` as `display_name`, `created_time` FROM `users` LEFT JOIN `employees` ON `users`.`uuid` = `employees`.`emp_id` WHERE `uuid` = ?";
        return $this->simpleGrabOne($db_instance, $sql, [$filter]);
    }

    public function loadFromUsernameAndPassword(Database $db_instance): ModelInterface
    {
        $sql = "SELECT `uuid`, `username`, `password`, `created_time` FROM `users` WHERE `username` = ? AND `password` = ?";
        return $this->simpleGrabOne($db_instance, $sql, [$this->username, $this->password]);
    }

    public function reload(Database $db_instance): ModelInterface
    {
        return $this->load($db_instance, $this->uuid);
    }

    public function create(Database $db_instance): bool
    {
        $sql = "INSERT INTO `users` (`uuid`, `username`, `password`, `created_time`) VALUES (:uuid, :username, :password, UNIX_TIMESTAMP())";
        return $this->simpleModifyFilled($db_instance, $sql);
    }

    public function replace(Database $db_instance): bool
    {
        if (isset($this->password)) {
            $sql = "UPDATE `users` SET `username` = :username, `password` = :password WHERE `uuid` = :uuid";
            return $this->simpleModifySafe($db_instance, $sql, ["uuid", "username", "password"]);
        } else {
            $sql = "UPDATE `users` SET `username` = :username WHERE `uuid` = :uuid";
            return $this->simpleModifySafe($db_instance, $sql, ["uuid", "username"]);
        }
    }

    public function destroy(Database $db_instance): bool
    {
        $sql = "DELETE FROM `users` WHERE `uuid` = :uuid";
        return $this->simpleModifySafe($db_instance, $sql, ["uuid"]);
    }

    /**
     * @return User
     */
    public function hashPassword(): static
    {
        $password = hash("sha256", $this->password);
        $this->setPassword($password);
        return $this;
    }

    public function jsonSerialize(): ?array
    {
        $result = $this->toArray();
        unset($result["password"]);
        return !empty($result) ? $result : null;
    }
}
