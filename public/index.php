<?php declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Api\TrinaxApiClient;
use App\Factory\HttpClientFactory;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$appEnv = getenv('APP_ENV');
$apiKey = getenv('TRINAX_API_KEY');
$baseUrl = getenv('TRINAX_BASE_URL');

if (!$apiKey) {
    throw new RuntimeException('TRINAX_API_KEY environment variable is not set');
}

$httpClient = HttpClientFactory::create($appEnv);

$client = new TrinaxApiClient($httpClient, $apiKey, $baseUrl);
$workplaces = $client->getWorkplaces();
$timeReports = $client->getTimeReports();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Time Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #f4f4f4;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Workplaces</h1>
    <ul>
        <?php foreach ($workplaces as $workplace): ?>
            <li><?php echo htmlspecialchars($workplace->name); ?></li>
        <?php endforeach; ?>
    </ul>

    <h1>Time Reports</h1>
    <ul>
        <?php foreach ($timeReports as $report): ?>
            <li>
                <?php echo htmlspecialchars($report->info); ?> -
                <?php echo htmlspecialchars((string)$report->hours); ?> hours
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>