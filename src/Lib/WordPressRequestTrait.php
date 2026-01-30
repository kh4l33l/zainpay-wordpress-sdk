<?php

namespace Zainpay\SDK\WordPress\Lib;

use Zainpay\SDK\Response;

trait WordPressRequestTrait
{
    public function post(string $url, array $data, array $headers = []): Response
    {
        $client = new WordPressHttpClient([
            'headers' => array_merge([
                'Content-type' => 'application/json',
                'Authorization' => "Bearer {$this->getToken()}"
            ], $headers)
        ]);

        try {
            $response = $client->post($url, [
                'json' => $data,
            ]);

            return new Response($response);
        } catch (\Exception $e) {
            $errorResponse = new Response($this->createErrorResponse($e));
            $errorResponse->setError(true);
            $errorResponse->setErrorMessage($e->getMessage());
            return $errorResponse;
        }
    }

    public function get(string $url, array $params = [], array $headers = []): Response
    {
        $client = new WordPressHttpClient([
            'headers' => array_merge([
                'Content-type' => 'application/json',
                'Authorization' => "Bearer {$this->getToken()}"
            ], $headers)
        ]);

        try {
            $response = $client->get($url, [
                'query' => $params,
            ]);

            return new Response($response);
        } catch (\Exception $e) {
            $errorResponse = new Response($this->createErrorResponse($e));
            $errorResponse->setError(true);
            $errorResponse->setErrorMessage($e->getMessage());
            return $errorResponse;
        }
    }

    public function patch(string $url, array $data, array $headers = []): Response
    {
        $client = new WordPressHttpClient([
            'headers' => array_merge([
                'Content-type' => 'application/json',
                'Authorization' => "Bearer {$this->getToken()}"
            ], $headers)
        ]);

        try {
            $response = $client->patch($url, [
                'json' => $data,
            ]);

            return new Response($response);
        } catch (\Exception $e) {
            $errorResponse = new Response($this->createErrorResponse($e));
            $errorResponse->setError(true);
            $errorResponse->setErrorMessage($e->getMessage());
            return $errorResponse;
        }
    }

    private function createErrorResponse(\Exception $e): WordPressHttpResponse
    {
        return new WordPressHttpResponse([
            'headers' => [],
            'body' => '',
            'response' => [
                'code' => 500,
                'message' => $e->getMessage(),
            ],
        ]);
    }
}
