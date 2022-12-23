<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

use JustinExample\Controllers\Authentic;
use JustinExample\Kernel\Router;
use JustinExample\Middleware\CORS;
use JustinExample\Middleware\Access;

(new Router(Authentic::class, "/authentic"))
    ->addMiddleware(true, CORS::class)
    ->addMiddleware(true, Access::class)
    ->register("POST", "/token", "postToken")
    ->channel();
