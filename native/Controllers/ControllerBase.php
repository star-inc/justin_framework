<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

require_once __DIR__ . '/../Kernel/Config.php';
require_once __DIR__ . '/../Kernel/Database.php';
require_once __DIR__ . '/../Kernel/Request.php';
require_once __DIR__ . '/../Kernel/Response.php';
require_once __DIR__ . '/ControllerInterface.php';

class ControllerBase implements ControllerInterface
{
    protected Request $request;
    protected Response $response;
    protected Config $config;
    protected Database $database;
    private array $middleware_before = [];
    private array $middleware_after = [];

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->config = new Config();
        $this->database = new Database($this->config);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function insertMiddleware(bool $type, string $task): static
    {
        $middleware = [&$this->middleware_before, &$this->middleware_after];
        array_push($middleware[(int)($type)], $task);
        return $this;
    }

    public function trigger(): void
    {
        foreach ($this->middleware_before as $class) {
            call_user_func("$class::trigger", $this);
        }
        $method = $this->request->getMethod();
        if (method_exists($this, "{$method}Action")) {
            $this->{"{$method}Action"}();
        } else {
            http_response_code(405);
            echo "Method Not Allowed";
        }
        foreach ($this->middleware_after as $class) {
            call_user_func("$class::trigger", $this);
        }
    }
}
