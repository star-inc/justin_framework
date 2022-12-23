<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use Psr\Http\Message\ResponseInterface as Psr7Response;

class Response
{
    private const COOKIES_ROOT = "/";
    private int $status = 200;
    private mixed $body;

    public function __construct(
        private Psr7Response $real_response
    ) {
    }

    /**
     * Set a header to redirect to another page.
     *
     * @param string $url
     * @return Response
     */
    public function redirect(string $url, bool $permanent = false): static
    {
        $this->setStatus($permanent ? 301 : 302);
        $this->real_response->withHeader("Location", $url);
        return $this;
    }

    /**
     * Set the http status code
     *
     * @param int $status
     * @return Response
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set a header to redirect to another page for upgrading
     *
     * @param string $url
     */
    public function redirectForUpgrade(string $url): static
    {
        $this->setStatus(307);
        header("Location: $url;", true, $this->status);
        return $this;
    }

    /**
     * Set a header
     */
    public function setHeader(string $key, string $value, bool $replace = true): static
    {
        header("$key: $value", $replace);
        return $this;
    }

    /**
     * Set content
     */
    public function setBody(mixed $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set a cookie
     */
    public function setCookie(string $key, string $value, int $expires): static
    {
        $cookie_root = self::COOKIES_ROOT;
        $this->real_response->withHeader(
            "Set-Cookie",
            "$key=$value; expires=$expires; path=$cookie_root; samesite=strict; httponly; secure"
        );
        return $this;
    }

    /**
     * Send an JSON response
     */
    public function sendJSON(bool $exit = false)
    {
        $this->real_response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $this->send($exit, json_encode($this->body));
    }

    /**
     * Send a response
     */
    public function send(bool $exit = false, ?string $deliver = null)
    {
        $this->real_response->withStatus($this->status);
        if ($this->status === 201 || $this->status === 204) {
            $deliver = "";
        }
        if (is_null($deliver) && !is_string($this->body)) {
            $deliver = serialize($this->body);
        }
        $this->real_response->getBody()->write(
            !is_null($deliver) ? $deliver : $this->body
        );
        if ($exit) throw new ExitException("Stop channel");
    }
}
