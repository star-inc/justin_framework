<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

class Request
{
    private UriInterface $request_uri;
    private string $ip_addr;
    private string $user_agent;
    private string $method;
    private StreamInterface $body;

    public function __construct(
        private Psr7Request $real_request
    ) {
        $all_server_params = $real_request->getServerParams();
        $this->request_uri = $real_request->getUri();
        $this->ip_addr = $all_server_params["REMOTE_ADDR"];
        $this->user_agent = $all_server_params["HTTP_USER_AGENT"];
        $this->method = $real_request->getMethod();
        $this->body = $real_request->getBody();
    }

    public static function assertKeysInData(Response $response, array|string $key, array $data): void
    {
        $key = is_array($key) ? $key : [$key];
        $result = array_filter($key, fn (string $name) => !array_key_exists($name, $data));
        if (empty($result)) return;
        $response->setStatus(400)->setBody([
            "status" => 400,
            "message" => "Bad Request",
            "reason" => "Missing the argument(s)",
            "missing" => $result
        ])->sendJSON(true);
    }

    public static function validData(Response $response, callable $validator, mixed $data): void
    {
        if (!(call_user_func($validator, $data))) {
            $response->setStatus(400)->setBody([
                "status" => 400,
                "message" => "Bad Request",
                "reason" => "Invalid data"
            ])->sendJSON(true);
        }
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->request_uri;
    }

    public function getRequestUriParsed(): UriInterface
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
        $all_get_params = $this->real_request->getQueryParams();
        return $all_get_params[$key] ?? "";
    }

    public function getCookie(string $key): string
    {
        $all_cookies = $this->real_request->getCookieParams();
        return $all_cookies[$key] ?? "";
    }

    public function getHeader(string $key): string
    {
        $key = strtoupper($key);
        $all_server_params = $this->real_request->getServerParams();
        return $all_server_params["HTTP_$key"] ?? "";
    }

    public function read(): array
    {
        $content_type = $this->getHeader("CONTENT_TYPE") ?? "application/x-www-form-urlencoded";
        $content_type_array = explode(";", $content_type);
        return match ($content_type_array[0]) {
            "application/x-www-form-urlencoded" => $this->readForm(),
            "application/json" => $this->readJSON(),
            default => []
        };
    }

    public function readForm(): array
    {
        parse_str($this->getBody(), $result);
        return $result;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body->getContents();
    }

    public function readJSON(): array
    {
        return json_decode($this->getBody(), true);
    }
}
