<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

$_CONFIG = array();

$_CONFIG["CORS"] = true;
$_CONFIG["CORS_DOMAIN"] = "http://localhost:8080";

$_CONFIG["DB_DSN"] = "mysql:host=127.0.0.1;dbname=justin";
$_CONFIG["DB_USERNAME"] = "justin_example";
$_CONFIG["DB_PASSWORD"] = "default_password";

return $_CONFIG;
