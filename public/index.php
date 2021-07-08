<?php

declare(strict_types=1);

use HackerspaceBielefeld\Website\Controller;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions(require __DIR__ . '/../config/services.php');

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

$app->add(TwigMiddleware::create(
    $app,
    $container->get(Slim\Views\Twig::class)
));

$app->get('/[page/{page}]', Controller\IndexController::class);

$app->get('/space/verein', Controller\VereinController::class);
$app->get('/space', Controller\UeberUnsController::class);

$app->run();
