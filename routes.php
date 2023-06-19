<?php
require 'vendor/autoload.php';

use MiladRahimi\PhpRouter\Router;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response\RedirectResponse;

require_once(__DIR__ . "\\Http\\Controllers\\CreationController.php");

$router = Router::create();

$router->post('/create/printer', [CreationController::class, 'printer']);
$router->post('/create/part', [CreationController::class, 'part']);
$router->post('/create/part-type', [CreationController::class, 'part_type']);

try {
    $router->dispatch();
} catch (RouteNotFoundException $e) {
    // It's 404!
    $router->getPublisher()->publish(new HtmlResponse('Not found.', 404));
} catch (Throwable $e) {
    // Log and report...
    $router->getPublisher()->publish(new HtmlResponse('Internal error.', 500));
}

?>