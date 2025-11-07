<?php declare(strict_types=1);

use App\Action\ListWorkplaceAction;
use App\Action\ListTimeReportAction;
use App\Action\CreateTimeReportAction;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../src/bootstrap.php';

$app = AppFactory::createFromContainer($container);
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