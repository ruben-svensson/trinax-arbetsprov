<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListWorkplaceAction
{

    public function __construct(
        private TrinaxApiClient $client,
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $workplaces = $this->client->getWorkplaces();

        $response->getBody()->write(json_encode($workplaces));
        return $response->withHeader('Content-Type', 'application/json');
    }
}