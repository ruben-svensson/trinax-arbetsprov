<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiServiceInterface;
use App\Database;
use App\Dto\CreateTimeReportDTO;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateTimeReportAction extends Action {

    public function __construct(
        private TrinaxApiServiceInterface $service,
        private Database $database
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        try {
            $postData = $request->getParsedBody();
            $createDto = CreateTimeReportDTO::fromArray($postData);

            $newReport = $this->service->createTimeReport($createDto);

            $this->database->linkImageToReport($newReport->id, 'test_not_real');

            return $this->jsonResponse($response, null, 201);
        } catch (InvalidArgumentException $e){
            return $this->jsonResponse($response, ['error' => $e->getMessage()], 400);
        }
    }
}