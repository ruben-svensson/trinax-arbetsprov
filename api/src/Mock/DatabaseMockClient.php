<?php declare(strict_types=1);
namespace App\Mock;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use PDO;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DatabaseMockClient implements ClientInterface {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();

        // Route to appropriate handler
        if (preg_match('#^/api/v1/workplace$#', $path) && $method === 'GET') {
            return $this->handleGetWorkplaces();
        }

        if (preg_match('#^/api/v1/timereport$#', $path) && $method === 'GET') {
            return $this->handleGetTimeReports($query);
        }

        if (preg_match('#^/api/v1/timereport/(\d+)$#', $path, $matches) && $method === 'GET') {
            return $this->handleGetTimeReport((int)$matches[1]);
        }

        if (preg_match('#^/api/v1/timereport$#', $path) && $method === 'POST') {
            return $this->handleCreateTimeReport($request);
        }

        // 404 for unknown routes
        return new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => 'Not found']));
    }

    private function handleGetWorkplaces(): ResponseInterface
    {
        $stmt = $this->pdo->query('SELECT id, name, created_time FROM mock_workplaces ORDER BY id');
        $workplaces = $stmt->fetchAll();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($workplaces));
    }

    private function handleGetTimeReports(string $query): ResponseInterface
    {
        parse_str($query, $params);

        $sql = 'SELECT id, workplace_id, date, hours, info FROM mock_timereports WHERE 1=1';
        $bindings = [];

        if (isset($params['workplace'])) {
            $sql .= ' AND workplace_id = ?';
            $bindings[] = (int)$params['workplace'];
        }

        if (isset($params['from_date'])) {
            $sql .= ' AND date >= ?';
            $bindings[] = $params['from_date'];
        }

        if (isset($params['to_date'])) {
            $sql .= ' AND date <= ?';
            $bindings[] = $params['to_date'];
        }

        $sql .= ' ORDER BY date DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        $reports = $stmt->fetchAll();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($reports));
    }

    private function handleGetTimeReport(int $id): ResponseInterface
    {
        $stmt = $this->pdo->prepare('SELECT id, workplace_id, date, hours, info FROM mock_timereports WHERE id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch();

        if (!$report) {
            return new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => 'Time report not found']));
        }

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($report));
    }

    private function handleCreateTimeReport(RequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);

        $stmt = $this->pdo->prepare(
            'INSERT INTO mock_timereports (workplace_id, date, hours, info) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $body['workplace_id'],
            $body['date'],
            $body['hours'],
            $body['info'] ?? null
        ]);

        $id = (int)$this->pdo->lastInsertId();

        // Fetch the created record to return it
        $stmt = $this->pdo->prepare('SELECT id, workplace_id, date, hours, info FROM mock_timereports WHERE id = ?');
        $stmt->execute([$id]);
        $newReport = $stmt->fetch();

        return new Response(201, ['Content-Type' => 'application/json'], json_encode($newReport));
    }
}