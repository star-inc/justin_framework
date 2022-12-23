<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Models;

use JsonSerializable;

interface ModelInterface extends JsonSerializable
{
    public function checkReady(): bool;

    public function fromArray(array $array): ModelInterface;

    public function toArray(): array;
}
