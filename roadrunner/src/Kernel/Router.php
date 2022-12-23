<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

use JustinExample\Controllers\ControllerInterface;
use JustinExample\Middleware\MiddlewareInterface;
use Exception;
use TypeError;

class Router
{
    private array $routes = [];
    private array $middleware_before = [];
    private array $middleware_after = [];
    private bool $allow_next_middleware = true;
    private ControllerInterface $controller;

    /**
     * Constructor.
     *
     * @param string $class
     * @param string $root_path
     */
    public function __construct(
        public string $class,
        public string $root_path = "/",
    ) {
        self::initRouterBridge();
        $this->controller = new $class();
    }

    /**
     * Initialize router bridge.
     *
     * @return void
     */
    private static function initRouterBridge(): void
    {
        global $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE;
        if (!isset($__JUSTIN_FRAMEWORK_ROUTER_BRIDGE)) {
            $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE = [
                "version" => "1.0",
                "channels" => []
            ];
        }
    }

    /**
     * Get data from the router bridge.
     *
     * @param string $key
     * @return mixed
     */
    private static function getRouterData(string $key): mixed
    {
        global $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE;
        return $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE[$key] ?? null;
    }

    /**
     * Set data to the router bridge.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private static function setRouterData(string $key, mixed $value): void
    {
        global $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE;
        $__JUSTIN_FRAMEWORK_ROUTER_BRIDGE[$key] = $value;
    }

    /**
     * Add a middleware to the router.
     *
     * @param bool $priority
     * @param string $middleware
     * @return Router
     */
    public function addMiddleware(bool $priority, string $controller): static
    {
        $instance = new $controller();
        if (!($instance instanceof MiddlewareInterface)) {
            throw new TypeError();
        }
        if ($priority) {
            $this->middleware_before[$controller] = $instance;
        } else {
            $this->middleware_after[$controller] = $instance;
        }
        return $this;
    }

    /**
     * Delete a middleware from the router.
     *
     * @param bool $priority
     * @param string $controller
     * @return Router
     */
    public function deleteMiddleware(bool $priority, string $controller): static
    {
        if ($priority) {
            unset($this->middleware_before[$controller]);
        } else {
            unset($this->middleware_after[$controller]);
        }
        return $this;
    }

    /**
     * Call next middleware
     *
     * @return void
     */
    private function nextMiddleware(): void
    {
        $this->allow_next_middleware = true;
    }

    /**
     * Execute the middleware.
     *
     * @param bool $priority_type
     * @param Context $context
     * @return void
     */
    private function executeMiddleware(bool $priority_type, Context $context): void
    {
        $middleware = $priority_type ? $this->middleware_before : $this->middleware_after;
        foreach ($middleware as $middleware) {
            if (is_null($middleware)) self::hung("middleware ($middleware) is null");
            assert($middleware instanceof MiddlewareInterface);
            $this->allow_next_middleware = false;
            $middleware::toUse($context, fn () => $this->nextMiddleware());
            if (!$this->allow_next_middleware) return;
        }
    }

    /**
     * Register a route.
     *
     * @param string $http_method
     * @param string $path
     * @param string|callable $method
     * @return Router
     */
    public function register(string $http_method, string $path, string|callable $method): static
    {
        $path = !empty($path) && !str_starts_with($path, "/") ? "/$path" : $path;
        $path = $this->root_path === "/" ? $path : $this->root_path . $path;
        if (!array_key_exists($path, $this->routes)) {
            $this->routes[$path] = [];
        }
        $this->routes[$path][$http_method] = $method;
        return $this;
    }

    /**
     * Send a message if the router is hung up.
     *
     * @param string $reason
     * @return void
     */
    private static function hung(string $reason): void
    {
        http_response_code(503);
        $message = "JustinHung: $reason";
        error_log($message);
        header($message);
        exit;
    }

    /**
     * Save the router bridge.
     *
     * @return void
     */
    public function channel(): void
    {
        $clazz = get_class($this->controller);
        $channels = self::getRouterData("channels");
        if (array_key_exists($clazz, $channels)) self::hung("channel ($clazz) is already registered");
        $channels[$clazz] = $this;
        self::setRouterData("channels", $channels);
    }

    /**
     * Execute all routers registered.
     *
     * @param Context $context
     * @return void
     */
    public static function run(Context $context): void
    {
        // Get channels already registered
        $channels = self::getRouterData("channels");
        // Get the http method
        $http_method = $context->getRequest()->getMethod();
        // Get the http path
        $http_path = $context->getRequest()->getRequestUriParsed()->getPath();
        // Get the path without the root path, and append the first slash
        $http_path = !str_starts_with($http_path, "/") ? "/$http_path" : $http_path;
        // Set found to false
        $found = false;
        // Start the loop of channels
        foreach ($channels as $channel) {
            // Assert that the channel is a router.
            assert($channel instanceof static);
            // Check the router exists
            if (!isset($channel->routes[$http_path][$http_method])) {
                if (isset($channel->routes[$http_path]["ANY"])) {
                    $http_method = "ANY";
                } else {
                    continue;
                }
            }
            // Find the method
            $method_name = $channel->routes[$http_path][$http_method];
            $method_name = "{$method_name}Action";
            // Check if the method exists
            if (!method_exists($channel->controller, $method_name)) {
                continue;
            }
            // Do middleware_before
            $channel->executeMiddleware(true, $context);
            if (!$channel->allow_next_middleware) {
                self::hung("middleware_before hung up");
            }
            // Execute the method
            $channel->controller->$method_name($context);
            // Do middleware_after
            $channel->executeMiddleware(false, $context);
            if (!$channel->allow_next_middleware) {
                self::hung("middleware_after hung up");
            }
            $found = true;
            break;
        }
        // If not found, send a 404 message
        if (!$found) {
            $context
                ->getResponse()
                ->setStatus(404)
                ->setBody([
                    "message" => "not found",
                    "description" => "no route register on $http_path"
                ])
                ->sendJSON();
        }
    }
}
