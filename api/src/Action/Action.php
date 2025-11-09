<?php declare(strict_types=1);
namespace App\Action;

use Psr\Http\Message\ResponseInterface;

abstract class Action {
    protected function jsonResponse(ResponseInterface $response, mixed $data, int $status = 200): ResponseInterface
    {
        if (is_array($data)) {
            $data = array_map(function ($item) {
                return method_exists($item, 'toArray') ? $item->toArray() : $item;
            }, $data);
        } elseif (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($json);
        
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}