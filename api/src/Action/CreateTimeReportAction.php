<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiClient;
use App\Database;
use App\Dto\CreateTimeReportDTO;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateTimeReportAction extends Action {

    public function __construct(
        private TrinaxApiClient $client,
        private Database $database
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        try {
            $postData = $request->getParsedBody();
            $createDto = CreateTimeReportDTO::fromRequest($postData);

            $newReport = $this->client->createTimeReport($createDto);

            $this->database->linkImageToReport($newReport->id, 'test_not_real');

            return $this->jsonResponse($response, $newReport, 201);
        } catch (InvalidArgumentException $e){
            return $this->jsonResponse($response, ['error' => $e->getMessage()], 400);
        }
    }
}