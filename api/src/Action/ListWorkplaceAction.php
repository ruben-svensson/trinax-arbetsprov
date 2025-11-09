<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListWorkplaceAction extends Action
{

    public function __construct(
        private TrinaxApiServiceInterface $service,
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $workplaces = $this->service->getWorkplaces();
        return $this->jsonResponse($response, $workplaces);
    }
}