<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ArticleService
{
    protected $client;
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $token;

    public function __construct($baseUrl, $username, $password)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    public function authenticate()
    {
        $response = $this->client->post('/wp-json/jwt-auth/v1/token', [
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password,
            ],
        ]);
        $data = json_decode($response->getBody(), true);
        $this->token = $data['token'] ?? null;
        return $this->token;
    }

    public function createArticle(array $data)
    {
        return $this->request('POST', '/wp-json/custom-api/v1/article', [
            'json' => $data,
        ]);
    }

    public function getArticle($id)
    {
        return $this->request('GET', "/wp-json/custom-api/v1/article/{$id}");
    }

    public function updateArticle($id, array $data)
    {
        return $this->request('PUT', "/wp-json/custom-api/v1/article/{$id}", [
            'json' => $data,
        ]);
    }

    public function uploadMedia($filePath)
    {
        return $this->request('POST', '/wp-json/custom-api/v1/upload-media', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ],
        ]);
    }

    protected function request($method, $uri, $options = [])
    {
        if (!$this->token) {
            $this->authenticate();
        }
        $options['headers']['Authorization'] = 'Bearer ' . $this->token;
        try {
            $response = $this->client->request($method, $uri, $options);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody(), true);
            }
            throw $e;
        }
    }
} 