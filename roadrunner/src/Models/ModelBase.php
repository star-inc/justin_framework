<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Models;

use JsonSerializable;
use BadMethodCallException;

abstract class ModelBase implements JsonSerializable
{
    public function fromArray(array $array): ModelInterface
    {
        foreach ($array as $key => $value) {
            $setter = 'set' .  str_replace("_", "", ucwords($key, "_"));
            try {
                $this->$setter($value);
            } catch (BadMethodCallException $e) {
                error_log($e->getMessage());
                $this->$key = $value;
            }
        }
        assert($this instanceof ModelInterface);
        return $this;
    }

    public function jsonSerialize(): ?array
    {
        $result = $this->toArray();
        return !empty($result) ? $result : null;
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    public function __call(string $method, array $args): mixed
    {
        if (!str_starts_with($method, "set") && !str_starts_with($method, "get")) {
            throw new BadMethodCallException("Method {$method} not found");
        }
        $property = str_replace(["set", "get"], "", $method);
        $property = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
        if (str_starts_with($method, "set")) {
            $this->{$property} = $args[0];
            return $this;
        }
        if (str_starts_with($method, "get") && property_exists($this, $property)) {
            return $this->{$property};
        }
    }
}
