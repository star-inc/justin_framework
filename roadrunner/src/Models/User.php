<?php

namespace JustinExample\Models;

use JustinExample\Kernel\Database;
use JustinExample\Kernel\Utils;
use BadMethodCallException;
use TypeError;

/**
 * Class User
 * @package JustinExample\Models
 * @property string $uuid
 * @property string $username
 * @property string $password
 * @property int $created_time
 * @property int $updated_time
 * @property array|null $roles
 * @method bool checkReady()
 * @method array batch(Database $database)
 * @method static load(Database $database, string $uuid)
 * @method static loadByUsername(Database $database, string $username)
 * @method static loadRoles(Database $database, string $uuid)
 * @method bool reload(Database $database)
 * @method bool create(Database $database)
 * @method bool replace(Database $database)
 * @method bool destroy(Database $database)
 * @method bool isAccessible(string $method, string $request_uri)
 * @method bool checkPassword(string $password)
 * @method static hashPassword()
 * @method array|null jsonSerialize()
 */
class User extends ModelBase implements ModelInterface
{
    public string $uuid;
    public string $username;
    public string $password;
    public int $created_time;
    public int $updated_time;
    public ?array $roles;

    use DatabaseUtils;

    public function checkReady(): bool
    {
        return isset($this->uuid);
    }

    public function batch(Database $db_instance): array
    {
        $sql = "SELECT `uuid`, `username`, `password`, `created_time`, `updated_time` FROM `users` ORDER BY `display_name` DESC";
        return $this->simpleGrabAll($db_instance, $sql);
    }

    public function load(Database $db_instance, $filter): ModelInterface
    {
        if (!is_string($filter)) {
            throw new TypeError();
        }
        $sql = "SELECT `uuid`, `username`, `password`, `created_time`, `updated_time` FROM `users` WHERE `uuid` = ?";
        return $this->simpleGrabOne($db_instance, $sql, [$filter]);
    }

    public function loadByUsername(Database $db_instance, string $username): ModelInterface
    {
        $sql = "SELECT `uuid`, `username`, `password`, `created_time`, `updated_time` FROM `users` WHERE `username` = ?";
        return $this->simpleGrabOne($db_instance, $sql, [$username]);
    }

    public function loadRoles(Database $db_instance): ModelInterface
    {
        $sql = "SELECT `uuid`, `name` FROM `user_role` LEFT JOIN `roles` ON `user_role`.`role_uuid` = `roles`.`uuid` WHERE `user_uuid` = ?";
        $roles = (new Role())->simpleGrabAll($db_instance, $sql, [$this->uuid]);
        $this->roles = array_map(fn (Role $role) => $role->loadActions($db_instance), $roles);
        return $this;
    }

    public function reload(Database $db_instance): ModelInterface
    {
        return $this->load($db_instance, $this->uuid);
    }

    public function create(Database $db_instance): bool
    {
        $sql = "INSERT INTO `users` (`uuid`, `username`, `password`, `created_time`, `updated_time`) VALUES (:uuid, :username, :password, :created_time, :updated_time)";
        return $this->simpleModifyFilled($db_instance, $sql);
    }

    public function replace(Database $db_instance): bool
    {
        $sql = "UPDATE `users` SET `username` = :username, `password` = :password, `level` = :level, `display_name` = :display_name, `address` = :address, `email` = :email, `phone` = :phone WHERE  `uuid` = :uuid";
        return $this->simpleModifyFilled($db_instance, $sql);
    }

    public function destroy(Database $db_instance): bool
    {
        $sql = "DELETE FROM `users` WHERE `uuid` = :uuid";
        return $this->simpleModifySafe($db_instance, $sql, ["uuid"]);
    }

    public function assignRole(Database $db_instance, Role $role): bool
    {
        $sql = "INSERT INTO `user_role` (`user_uuid`, `role_uuid`) VALUES (?, ?)";
        $stmt = $db_instance->getClient()->prepare($sql);
        return $stmt->execute([$this->uuid, $role->uuid]);
    }

    public function isAccessible(string $method, string $path): bool
    {
        if (!isset($this->roles)) {
            throw new BadMethodCallException("user roles not loaded");
        }
        foreach ($this->roles as $role) {
            assert($role instanceof Role);
            if ($role->isAccessible($method, $path)) {
                return true;
            }
        }
        return false;
    }

    public function checkPassword(string $password): bool
    {
        return Utils::passwordVerify($password, $this->password);
    }

    public function hashPassword(): static
    {
        $this->password = Utils::passwordHash($this->password);
        return $this;
    }

    public function jsonSerialize(): ?array
    {
        $result = $this->toArray();
        unset($result["password"]);
        return !empty($result) ? $result : null;
    }
}
