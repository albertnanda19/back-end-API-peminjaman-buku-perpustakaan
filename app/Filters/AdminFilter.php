<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use CodeIgniter\Config\Services;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $key = getenv('JWT_SECRET');
            $authHeader = $request->getServer('HTTP_AUTHORIZATION');
            list($token) = sscanf($authHeader, 'Bearer %s');

            if ($token) {
                $decoded = JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));

                $request->decodedToken = $decoded;

                if ($decoded->role !== 'admin') {
                    return Services::response()
                        ->setJSON(['error' => 'Access denied, you are not an admin'])
                        ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }
            } else {
                return Services::response()
                    ->setJSON(['error' => 'Token required'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
        } catch (ExpiredException $e) {
            return Services::response()
                ->setJSON(['error' => 'Token expired'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['error' => 'An error occurred while validating token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Optional implementation if needed after the request
    }
}
