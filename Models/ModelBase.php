<?php
// Justin PHP Framework
// (c)2021 SuperSonic(https://randychen.tk)

use JetBrains\PhpStorm\Pure;

require_once __DIR__ . '/ModelInterface.php';

class ModelBase implements JsonSerializable
{
    public function fromArray(array $array): static
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    #[Pure]
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
