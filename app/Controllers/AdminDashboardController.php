<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminDashboardController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $admin = $request->getAttribute('admin');

        ob_start();
        $pageTitle = 'Dashboard';
        include __DIR__ . '/../../views/pages/admin/dashboard.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}
