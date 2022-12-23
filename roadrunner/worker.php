<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/public/index.php";

use Nyholm\Psr7;
use Spiral\RoadRunner;
use JustinExample\Kernel\Context;
use JustinExample\Kernel\Router;
use JustinExample\Middleware\CORS;

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while ($req = $worker->waitRequest()) {
    $rsp = new Psr7\Response();
    try {
        $context = new Context($req, $rsp);
        CORS::preflight($context);
        Router::run($context);
        $worker->respond($rsp);
    } catch (\JustinExample\Kernel\ExitException $e) {
        $worker->respond($rsp);
    } catch (\Throwable $e) {
        $worker->getWorker()->error((string)$e);
    }
}
