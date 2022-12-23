<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Controllers;

use JustinExample\Kernel\Context;
use JustinExample\Models\User;
use JustinExample\Validators\JWT;

final class Authentic implements ControllerInterface
{
    public function postTokenAction(Context $context): void
    {
        $form = $context->getRequest()->read();
        if (!isset($form['username']) || !isset($form['password'])) {
            $context->getResponse()->setStatus(400)->setBody(["message" => "bad request"])->sendJSON();
            return;
        }
        $user = new User();
        $user->loadByUsername($context->getDatabase(), $form['username']);
        if ($user->checkReady() && $user->checkPassword($form['password'])) {
            $jwt = (new JWT($context))->issue($user);
            $context->getResponse()->setStatus(200)->setBody(["token" => $jwt])->sendJSON();
        } else {
            $context->getResponse()->setStatus(401)->setBody(["message" => "unauthorized"])->sendJSON();
        }
    }
}
