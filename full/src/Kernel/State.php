<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

class State
{
    private array $memory;

    public function __construct()
    {
        $this->memory = [];
    }

    public function get(string $key)
    {
        return $this->memory[$key] ?? null;
    }

    public function set(string $key, $value)
    {
        $this->memory[$key] = $value;
    }

    public function del(string $key)
    {
        unset($this->memory[$key]);
    }
}
