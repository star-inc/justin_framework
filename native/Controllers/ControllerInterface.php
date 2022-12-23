<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

interface ControllerInterface
{
    public function getRequest(): Request;

    public function getResponse(): Response;

    public function getConfig(): Config;

    public function getDatabase(): Database;

    public function trigger(): void;
}
