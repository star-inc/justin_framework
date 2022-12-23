<?php

namespace JustinExample\Validators;

interface ValidatorInterface
{
    public function validate(mixed $value): mixed;
}
