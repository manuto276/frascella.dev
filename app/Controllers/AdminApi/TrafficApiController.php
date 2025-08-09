<?php
namespace App\Controllers\AdminApi;

use App\Services\TrafficService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TrafficApiController
{
    public function __construct(private TrafficService $traffic) {}

    public function summary(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        $data = $this->traffic->getSummary($q['from'] ?? null, $q['to'] ?? null);

        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function timeseries(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        $data = $this->traffic->getTimeSeries($q['from'] ?? null, $q['to'] ?? null);

        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
