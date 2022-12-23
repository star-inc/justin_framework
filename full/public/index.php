<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use JustinExample\Controllers\Authentic;
use JustinExample\Kernel\Router;
use JustinExample\Middleware\CORS;
use JustinExample\Middleware\Access;

(new Router(Authentic::class, "/authentic"))
    ->addMiddleware(true, CORS::class)
    ->addMiddleware(true, Access::class)
    ->register("GET", "/session", "getSession")
    ->register("POST", "/session", "postSession")
    ->register("DELETE", "/session", "deleteSession")
    ->channel();

CORS::preflight();
Router::run();
