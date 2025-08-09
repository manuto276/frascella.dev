<?php
namespace App\Controllers\PublicApi;

use App\Services\ContactService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContactApiController
{
    public function __construct(private ContactService $contact) {}

    public function submit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)($request->getParsedBody() ?? []);
        $ip   = $request->getServerParams()['REMOTE_ADDR'] ?? null;
        $ua   = $request->getHeaderLine('User-Agent');
        $ref  = $request->getHeaderLine('Referer');

        // Validazione
        $name    = trim($data['name']    ?? '');
        $email   = trim($data['email']   ?? '');
        $subject = trim($data['subject'] ?? '');
        $message = trim($data['message'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
            $response->getBody()->write(json_encode(['error' => 'invalid']));
            return $response->withStatus(422)->withHeader('Content-Type','application/json');
        }

        // Rate limit
        if ($this->contact->isRateLimited($ip ?? '')) {
            $response->getBody()->write(json_encode(['error' => 'rate_limited']));
            return $response->withStatus(429)->withHeader('Content-Type','application/json');
        }

        // Store + notify
        $id = $this->contact->store([
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject ?: null,
            'message'    => $message,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'referrer'   => $ref,
        ]);

        $this->contact->notify([
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);

        $response->getBody()->write(json_encode(['ok' => true, 'id' => $id]));
        return $response->withHeader('Content-Type','application/json');
    }
}
