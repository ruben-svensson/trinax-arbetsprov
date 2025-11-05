<?php
namespace App\Api;

use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Psr7\Request;
use App\Dto\WorkplaceDTO;
use DateTimeImmutable;

class TrinaxApiClient {
    private ClientInterface $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(ClientInterface $client, string $apiKey, string $baseUrl) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return WorkplaceDTO[]
     */
    public function getWorkplaces(): array {
        $req = new Request('GET', $this->baseUrl . 'workplace', [
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ]);

        $res = $this->client->sendRequest($req);
        $data = json_decode($res->getBody()->getContents(), true);

        $workplaces = [];
        foreach ($data as $workplaceData){
            $workplace = WorkplaceDTO::create(
                $workplaceData['id'],
                $workplaceData['name'],
                new DateTimeImmutable($workplaceData['createdTime'])
            );

            $workplaces[] = $workplace;
        }

        return $workplaces;
    }
}