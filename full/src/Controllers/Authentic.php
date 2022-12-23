<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Controllers;

use JustinExample\Kernel\Context;
use JustinExample\Models\User;

final class Authentic implements ControllerInterface
{
    public function getSessionAction(Context $context): void
    {
        $user = $context->getState()->get("user");
        if ($user instanceof User && $user->checkReady()) {
            $context->getResponse()->setStatus(200)->setBody($user)->sendJSON();
        } else {
            $context->getResponse()->setStatus(401)->setBody(["message" => "unauthorized"])->sendJSON();
        }
    }

    public function postSessionAction(Context $context): void
    {
        $form = $context->getRequest()->read();
        if (!isset($form['username']) || !isset($form['password'])) {
            $context->getResponse()->setStatus(400)->setBody(["message" => "bad request"])->sendJSON();
            return;
        }
        $user = new User();
        $user
            ->setUsername($form['username'])
            ->setPassword($form['password'])
            ->hashPassword()
            ->loadFromUsernameAndPassword($context->getDatabase());
        if ($user->checkReady()) {
            $context->getSession()->set("user_id", $user->getUuid());
            $context->getResponse()->setStatus(201)->send();
        } else {
            $context->getResponse()->setStatus(401)->setBody(["message" => "unauthorized"])->sendJSON();
        }
    }

    public function deleteSessionAction(Context $context): void
    {
        $user = $context->getState()->get("user");
        if ($user instanceof User && $user->checkReady()) {
            $context->getSession()->del("user_id");
            $context->getResponse()->setStatus(204)->send();
        } else {
            $context->getResponse()->setStatus(401)->setBody(["message" => "unauthorized"])->sendJSON();
        }
    }
}
