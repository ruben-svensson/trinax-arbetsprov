<?php declare(strict_types=1);

use App\Database;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../src/bootstrap.php';

$app = AppFactory::createFromContainer($container);

$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: $container->get('app.env') === 'development',
    logErrors: true,
    logErrorDetails: true
);

$app->get('/api/workplace', \App\Action\ListWorkplaceAction::class);
$app->get('/api/timereport[/{id}]', \App\Action\ListTimeReportAction::class);
$app->post('/api/timereport', \App\Action\CreateTimeReportAction::class);
$app->get('/testlink', function ($request, $response) {
    $db = $this->get(Database::class);
    $db->linkImageToReport(1, 'test.jpg');

    $response->getBody()->write("Test link is working!");
    return $response;
});

$app->run();