<?php
namespace App\Controllers;

use App\Database\Connection;
use App\Services\AdminService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SetupController
{
    public function __construct(private Connection $connection, private AdminService $adminService) {}

    public function form(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->adminService->shouldShowSetupForm()) {
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../views/pages/admin/setup.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function submit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) ($request->getParsedBody() ?? []);

        $name     = trim($data['name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $confirm  = (string) ($data['password_confirm'] ?? '');

        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (!empty($errors)) {
            ob_start();
            $formErrors = $errors;
            include __DIR__ . '/../../views/admin/setup.php';
            $html = ob_get_clean();
            $response->getBody()->write($html);
            return $response;
        }

        // Salvo l'admin
        $stmt = $this->connection->getPdo()->prepare("
            INSERT INTO admins (name, email, password_hash, created_at)
            VALUES (:name, :email, :password_hash, NOW())
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Go to the complete page
        return $response->withHeader('Location', '/setup/complete')->withStatus(302);
    }

    public function complete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // If an admin already exists, we can show the choice screen; otherwise redirect back to setup
        if ($this->adminService->shouldShowSetupForm()) {
            return $response->withHeader('Location', '/setup')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../views/pages/admin/setup-complete.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}
