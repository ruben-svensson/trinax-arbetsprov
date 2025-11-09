<?php declare(strict_types=1);
namespace App\Action;

use App\Database;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListTimeReportImageAction extends Action {

    public function __construct(
        private Database $database
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        // Validate the ID parameter
        if (!isset($args['id']) || !is_numeric($args['id'])) {
            return $this->jsonResponse($response, ['error' => 'Invalid time report ID'], 400);
        }

        $reportId = (int) $args['id'];

        try {
            $filename = $this->database->getImageForReport($reportId);

            if ($filename === null) {
                return $this->jsonResponse($response, ['error' => 'Image not found'], 404);
            }

            $imagePath = '/app/uploads/' . $filename;

            if (!file_exists($imagePath)) {
                return $this->jsonResponse($response, ['error' => 'Image file not found on disk'], 404);
            }

            $imageData = file_get_contents($imagePath);

            $mimeType = mime_content_type($imagePath);

            $response->getBody()->write($imageData);

            return $response
                ->withHeader('Content-Type', $mimeType)
                ->withHeader('Content-Length', (string) filesize($imagePath))
                ->withHeader('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year

        } catch (\Exception $e) {
            return $this->jsonResponse($response, ['error' => 'Failed to retrieve image'], 500);
        }
    }
}