<?php declare(strict_types=1);
namespace App;

use DI\Attribute\Inject;
use PDO;
use RuntimeException;

class Database {

    private string $imageTableName;

    public function __construct(
        private PDO $pdo,
        #[Inject('app.env')] string $environment
    ) {
        // Choose the table name based on the environment
        $this->imageTableName = match ($environment) {
            'development' => 'mock_timereport_images',
            'production' => 'timereport_images',
            default => throw new RuntimeException('Invalid environment for database service'),
        };
    }
    
    public function linkImageToReport(int $reportId, string $filename): void {
        $sql = "INSERT INTO {$this->imageTableName} (filename, timereport_id) VALUES (:filename, :timereport_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':filename' => $filename,
            ':timereport_id' => $reportId,
        ]);
    }
}