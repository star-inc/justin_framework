<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use InvalidArgumentException;

class Request
{
    private string $request_uri;
    private string $ip_addr;
    private string $user_agent;
    private string $method;

    public function __construct()
    {
        $this->request_uri = $_SERVER["REQUEST_URI"];
        $this->ip_addr = $_SERVER["REMOTE_ADDR"] ?? "";
        $this->user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "";
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    public static function assertKeysInData(Response $response, $key, array $data): void
    {
        $key = is_array($key) ? $key : [$key];
        $result = array_filter($key, fn($name) => !array_key_exists($name, $data));
        if (empty($result)) return;
        throw new InvalidArgumentException();
    }

    public static function validData(Response $response, callable $validator, $data): void
    {
        if (!(call_user_func($validator, $data))) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->request_uri;
    }

    /**
     * @return string
     */
    public function getRemoteIp(): string
    {
        return $this->ip_addr;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->user_agent;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    public function getQuery(string $key): string
    {
        return $_GET[$key] ?? "";
    }

    public function getCookie(string $key): string
    {
        return $_COOKIE[$key] ?? "";
    }

    public function getHeader(string $key): string
    {
        $key = strtoupper($key);
        return $_SERVER["HTTP_$key"] ?? "";
    }
}
