<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

$_CONFIG = array();

$_CONFIG["CORS"] = false;
$_CONFIG["CORS_DOMAIN"] = "";

$_CONFIG["DB_DSN"] = $_ENV["DB_DSN"];
$_CONFIG["DB_USERNAME"] = $_ENV["DB_USERNAME"];
$_CONFIG["DB_PASSWORD"] = $_ENV["DB_PASSWORD"];

return $_CONFIG;
