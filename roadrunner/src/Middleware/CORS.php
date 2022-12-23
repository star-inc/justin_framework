<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Middleware;

use JustinExample\Kernel\Context;

class CORS implements MiddlewareInterface
{
    public const METHOD_GET = "GET";
    public const METHOD_POST = "POST";
    public const METHOD_PUT = "PUT";
    public const METHOD_PATCH = "PATCH";
    public const METHOD_DELETE = "DELETE";
    public const METHOD_OPTIONS = "OPTIONS";

    public static function preflight(Context $context): void
    {
        if ($context->getRequest()->getMethod() === self::METHOD_OPTIONS) {
            self::toUse($context, fn () => null);
            $context->getResponse()->setStatus(204)->send(true);
        }
    }

    public static function toUse(Context $context, callable $next): void
    {
        if (is_null($config = GeneralCORS::policy($context))) return;
        $context
            ->getResponse()
            ->setHeader("Access-Control-Allow-Origin", $config->getAllowOrigin())
            ->setHeader("Access-Control-Allow-Methods", implode(", ", $config->getAllowMethods()))
            ->setHeader("Access-Control-Allow-Headers", implode(", ", $config->getAllowHeaders()));
        if ($config->getAllowCredentials()) {
            $context
                ->getResponse()
                ->setHeader("Access-Control-Allow-Credentials", "true");
        }
        $next();
    }
}
