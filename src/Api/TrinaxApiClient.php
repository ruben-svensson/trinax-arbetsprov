<?php
namespace App\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Dto\WorkplaceDTO;
use DateTimeImmutable;

class TrinaxApiClient {
    private Client $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl) {
        $this->client = new Client();
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