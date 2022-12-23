<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

class Response
{
    private const COOKIES_ROOT = "/";

    public function setHeader(string $key, string $value, bool $replace = true): Response
    {
        header("$key: $value", $replace);
        return $this;
    }

    public function setCookie(string $key, string $value, int $expires): Response
    {
        setcookie($key, $value, [
            "expires" => $expires,
            'path' => self::COOKIES_ROOT,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        return $this;
    }
}
