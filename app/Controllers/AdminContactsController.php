<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminContactsController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        ob_start();
        $pageTitle = 'Contacts';
        include __DIR__ . '/../../views/pages/admin/contacts/index.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
