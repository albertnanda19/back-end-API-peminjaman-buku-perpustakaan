<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;
use App\Models\BookModel;
use \Firebase\JWT\Key;

class BooksController extends ResourceController
{
    protected $modelName = 'App\Models\BukuModel';
    protected $format = 'json';

    public function index()
    {
        $authHeader = $this->request->getServer('HTTP_AUTHORIZATION');
        $token = $this->getTokenFromHeader($authHeader);

        if (!$token) {
            return $this->failUnauthorized('Missing or invalid token');
        }

        $publicKey = file_get_contents('../public_key.pem');

        try {
            $decodedToken = JWT::decode($token, new Key($publicKey, 'RS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->failUnauthorized('Token expired');
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return $this->failUnauthorized('Token not yet valid');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->failUnauthorized('Invalid signature');
        } catch (\UnexpectedValueException $e) {
            return $this->failUnauthorized('Unexpected value');
        } catch (\Exception $e) {
            return $this->failUnauthorized('Error decoding token');
        }

        $bukuModel = new BookModel();
        $books = $bukuModel->findAll();

        return $this->respond($books);
    }

    private function getTokenFromHeader($authHeader)
    {
        if (!$authHeader) {
            return null;
        }

        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            return null;
        }

        return $jwt;
    }
}
