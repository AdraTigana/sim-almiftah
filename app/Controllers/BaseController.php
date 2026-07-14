<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $session;
    protected $data;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
        $this->data = [
            'userName' => $this->session->get('nama') ?? '',
            'userRole' => $this->session->get('role') ?? '',
        ];
    }

    protected function render(string $view, array $data = []): string
    {
        $data = array_merge($this->data, $data);
        return view($view, $data);
    }
}
