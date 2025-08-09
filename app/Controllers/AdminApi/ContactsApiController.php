<?php
namespace App\Controllers\AdminApi;

use App\Services\ContactService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContactsApiController
{
    public function __construct(private ContactService $contacts) {}

    public function latest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $limit = (int)($request->getQueryParams()['limit'] ?? 5);
        $data = $this->contacts->latest($limit);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type','application/json');
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        $data = $this->contacts->search($q['status'] ?? null, $q['from'] ?? null, $q['to'] ?? null,
                                        (int)($q['page'] ?? 1), (int)($q['per'] ?? 20));
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type','application/json');
    }

    public function updateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $payload = (array)($request->getParsedBody() ?? []);
        $status = $payload['status'] ?? null;
        $ok = $status && in_array($status, ['new','read','replied','archived'], true) && $this->contacts->updateStatus($id, $status);
        $response->getBody()->write(json_encode(['ok'=>$ok]));
        return $response->withHeader('Content-Type','application/json');
    }
}
