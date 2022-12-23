<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

use JetBrains\PhpStorm\Pure;

require_once __DIR__ . '/ModelInterface.php';

class ModelBase implements JsonSerializable
{
    public function fromArray(array $array): ModelInterface
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
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
}
