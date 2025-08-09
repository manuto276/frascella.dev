<?php
namespace App\Services;

use App\Database\Connection;
use DateTimeImmutable;

class TrafficService
{
    public function __construct(private Connection $connection) {}

    private function rangeOrDefault(?string $from, ?string $to): array
    {
        // Default: last 7 days
        $end = $to ? new DateTimeImmutable($to) : new DateTimeImmutable('now');
        $start = $from ? new DateTimeImmutable($from) : $end->modify('-6 days');
        return [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')];
    }

    public function getSummary(?string $from = null, ?string $to = null): array
    {
        [$fromDt, $toDt] = $this->rangeOrDefault($from, $to);
        $pdo = $this->connection->getPdo();

        // total hits
        $total = (int)$pdo->prepare("
            SELECT COUNT(*) FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
        ")->execute([':from' => $fromDt, ':to' => $toDt]) ?: 0;

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS c FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $total = (int) $stmt->fetchColumn();

        // unique IPs
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT ip_address) FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $uniqueIps = (int) $stmt->fetchColumn();

        // avg response time
        $stmt = $pdo->prepare("
            SELECT AVG(response_time_ms) FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $avgRt = (float) ($stmt->fetchColumn() ?: 0);

        // top paths
        $stmt = $pdo->prepare("
            SELECT path, COUNT(*) AS hits
            FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
            GROUP BY path
            ORDER BY hits DESC
            LIMIT 10
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $topPaths = $stmt->fetchAll();

        return [
            'range'      => ['from' => $fromDt, 'to' => $toDt],
            'total'      => $total,
            'unique_ips' => $uniqueIps,
            'avg_rt_ms'  => round($avgRt, 1),
            'top_paths'  => $topPaths,
        ];
    }

    public function getTimeSeries(?string $from = null, ?string $to = null): array
    {
        [$fromDt, $toDt] = $this->rangeOrDefault($from, $to);
        $pdo = $this->connection->getPdo();

        // Group by day
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) AS d, COUNT(*) AS hits
            FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
            GROUP BY DATE(created_at)
            ORDER BY d ASC
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $rows = $stmt->fetchAll();

        // Also method split (GET/POST/etc.)
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) AS d, method, COUNT(*) AS c
            FROM traffic_logs
            WHERE created_at BETWEEN :from AND :to
            GROUP BY DATE(created_at), method
            ORDER BY d ASC
        ");
        $stmt->execute([':from' => $fromDt, ':to' => $toDt]);
        $byMethod = $stmt->fetchAll();

        return [
            'range'     => ['from' => $fromDt, 'to' => $toDt],
            'daily'     => $rows,
            'by_method' => $byMethod,
        ];
    }
}
