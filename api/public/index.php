<?php declare(strict_types=1);

use App\Action\ListWorkplaceAction;
use App\Action\ListTimeReportAction;
use App\Action\CreateTimeReportAction;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../src/bootstrap.php';

$app = AppFactory::createFromContainer($container);

// Fix cors to work in development
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: $container->get('app.env') === 'development',
    logErrors: true,
    logErrorDetails: true
);

$app->get('/api/workplace', ListWorkplaceAction::class);
$app->get('/api/timereport[/{id}]', ListTimeReportAction::class);
$app->post('/api/timereport', CreateTimeReportAction::class);

$app->run();