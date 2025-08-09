<?php

namespace App\Services;

use App\Database\Connection;
use DateTimeImmutable;
use PDO;

class ContactService
{
    public function __construct(private Connection $connection, private MailerService $mailer, private array $config) {}

    public function isRateLimited(string $ipAddress, int $limit = 3, int $windowMinutes = 10): bool
    {
        $pdo = $this->connection->getPdo();
        $since = (new DateTimeImmutable())->modify("-{$windowMinutes} minutes")->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM contact_forms 
            WHERE ip_address = :ip AND created_at >= :since
        ");
        $stmt->execute(['ip' => $ipAddress, 'since' => $since]);
        $count = (int)$stmt->fetchColumn();

        return $count >= $limit;
    }

    public function store(array $data): int
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO contact_forms (name, email, subject, message, ip_address, user_agent, referrer)
            VALUES (:name, :email, :subject, :message, :ip_address, :user_agent, :referrer)
        ");

        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'referrer' => $data['referrer'] ?? null,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function notify(array $data): bool
    {
        $to = $this->config['to'];
        $subject = "[Portfolio] New contact message";
        $html = sprintf(
            '<p><strong>Name:</strong> %s</p><p><strong>Email:</strong> %s</p><p><strong>Subject:</strong> %s</p><p><strong>Message:</strong><br>%s</p><hr><p><small>IP: %s<br>User-Agent: %s</small></p>',
            htmlspecialchars($data['name']),
            htmlspecialchars($data['email']),
            htmlspecialchars($data['subject']),
            nl2br(htmlspecialchars($data['message'])),
            htmlspecialchars($data['ip_address']),
            htmlspecialchars($data['user_agent'])
        );

        return $this->mailer->send(
            $to['address'],
            $to['name'],
            $subject,
            $html,
            null // No plain text version
        );
    }

    public function latest(int $limit = 5): array
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("SELECT id, name, email, subject, created_at, status
                           FROM contact_forms
                           ORDER BY created_at DESC
                           LIMIT :lim");
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function search(?string $status, ?string $from, ?string $to, int $page = 1, int $per = 20): array
    {
        $pdo = $this->connection->getPdo();
        $where = [];
        $params = [];
        if ($status) {
            $where[] = 'status = :status';
            $params[':status'] = $status;
        }
        if ($from) {
            $where[] = 'created_at >= :from';
            $params[':from'] = $from . ' 00:00:00';
        }
        if ($to) {
            $where[] = 'created_at <= :to';
            $params[':to']   = $to . ' 23:59:59';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $offset = max(0, ($page - 1) * $per);

        $count = $pdo->prepare("SELECT COUNT(*) FROM contact_forms $whereSql");
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $sql = "SELECT id, name, email, subject, message, created_at, status
            FROM contact_forms $whereSql
            ORDER BY created_at DESC
            LIMIT :per OFFSET :off";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(\PDO::FETCH_ASSOC), 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public function updateStatus(int $id, string $status): bool
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("UPDATE contact_forms SET status = :s WHERE id = :id");
        return $stmt->execute([':s' => $status, ':id' => $id]);
    }
}
