<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class InternalFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah request memiliki header 'X-Internal-Request'
        // 2. Dan nilainya harus 'TRUE'
        if ($request->getHeaderLine('X-Internal-Request') !== 'TRUE') {
            // Jika tidak punya header rahasia -> Tolak! Arahkan ke Login
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}