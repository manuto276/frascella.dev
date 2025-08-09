<?php

namespace App\Middlewares;

use App\Database\Connection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class TrafficLogger implements MiddlewareInterface
{
    public function __construct(private Connection $connection) {}

    public function process(Request $request, Handler $handler): Response
    {
        $start = microtime(true);

        $path = $request->getUri()->getPath();
        if (preg_match('#^/(css|js|images|assets|favicon\.ico)#i', $path)) {
            return $handler->handle($request);
        }

        $response = $handler->handle($request);
        $durationMs = (int) round((microtime(true) - $start) * 1000);

        $server = $request->getServerParams();
        $headers = [];
        foreach ($request->getHeaders() as $k => $v) {
            $headers[$k] = implode(', ', $v);
        }

        $isAjax = ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') ? 1 : 0;

        $stmt = $this->connection->getPdo()->prepare("
            INSERT INTO traffic_logs (
                session_id, ip_address, user_agent, method, scheme, host, path,
                query_string, full_url, referrer, accept_language, accept_header,
                route_name, status_code, response_time_ms, is_ajax, headers
            )
            VALUES (:session_id, :ip, :ua, :method, :scheme, :host, :path,
                    :query, :full, :ref, :al, :ah, :route, :status, :rt, :ajax, :headers)
        ");

        $stmt->execute([
            ':session_id' => session_id() ?: null,
            ':ip'         => $server['REMOTE_ADDR'] ?? '',
            ':ua'         => $request->getHeaderLine('User-Agent') ?: null,
            ':method'     => $request->getMethod(),
            ':scheme'     => $request->getUri()->getScheme(),
            ':host'       => $request->getUri()->getHost(),
            ':path'       => $path,
            ':query'      => $request->getUri()->getQuery() ?: null,
            ':full'       => (string) $request->getUri(),
            ':ref'        => $request->getHeaderLine('Referer') ?: null,
            ':al'         => $request->getHeaderLine('Accept-Language') ?: null,
            ':ah'         => $request->getHeaderLine('Accept') ?: null,
            ':route'      => $request->getAttribute('__route__')?->getName() ?? null,
            ':status'     => $response->getStatusCode(),
            ':rt'         => $durationMs,
            ':ajax'       => $isAjax,
            ':headers'    => json_encode($headers, JSON_UNESCAPED_UNICODE),
        ]);

        return $response;
    }
}
