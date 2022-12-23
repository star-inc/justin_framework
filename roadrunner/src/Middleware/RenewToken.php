<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Middleware;

use JustinExample\Kernel\Context;
use JustinExample\Models\User;
use JustinExample\Validators\JWT;

class RenewToken implements MiddlewareInterface
{
    public static function toUse(Context $context, callable $next): void
    {
        $user = $context->getState()->get("user");
        if ($user instanceof User && $user->checkReady()) {
            $jwt = (new JWT($context))->issue($user);
            $context->getResponse()->setHeader('X-Next-Token', $jwt);
        }
        $next();
    }
}
