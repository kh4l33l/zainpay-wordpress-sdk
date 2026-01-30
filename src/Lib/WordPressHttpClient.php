<?php

namespace Zainpay\SDK\WordPress\Lib;

use Zainpay\SDK\Response;

class WordPressHttpClient
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function post(string $url, array $options = []): WordPressHttpResponse
    {
        $args = $this->buildRequestArgs('POST', $options);
        $response = wp_remote_post($url, $args);
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }
        return new WordPressHttpResponse($response);
    }

    public function get(string $url, array $options = []): WordPressHttpResponse
    {
        if (isset($options['query'])) {
            $url = add_query_arg($options['query'], $url);
        }
        $args = $this->buildRequestArgs('GET', $options);
        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }
        return new WordPressHttpResponse($response);
    }

    public function patch(string $url, array $options = []): WordPressHttpResponse
    {
        $args = $this->buildRequestArgs('PATCH', $options);
        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }
        return new WordPressHttpResponse($response);
    }

    private function buildRequestArgs(string $method, array $options = []): array
    {
        $args = [
            'method' => $method,
            'timeout' => $this->config['timeout'] ?? 30,
            'headers' => $this->config['headers'] ?? [],
        ];

        if (isset($options['json'])) {
            $args['body'] = wp_json_encode($options['json']);
            $args['headers']['Content-Type'] = 'application/json';
        }

        if (isset($options['form_params'])) {
            $args['body'] = $options['form_params'];
        }

        return $args;
    }
}


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

class WordPressHttpResponse implements \Psr\Http\Message\ResponseInterface
{
    private array $headers = [];
    private array $headerMap = [];
    private string $protocolVersion = '1.1';
    private int $statusCode = 200;
    private string $reasonPhrase = '';
    private WordPressHttpStream $body;

    public function __construct($response)
    {
        $this->statusCode = (int) wp_remote_retrieve_response_code($response);
        $this->reasonPhrase = (string) wp_remote_retrieve_response_message($response);
        $this->body = new WordPressHttpStream(wp_remote_retrieve_body($response));
        $this->setHeaders(wp_remote_retrieve_headers($response));
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): WordPressHttpStream
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version): self
    {
        $clone = clone $this;
        $clone->protocolVersion = (string) $version;
        return $clone;
    }

    public function hasHeader($name): bool
    {
        $key = strtolower((string) $name);
        return array_key_exists($key, $this->headerMap);
    }

    public function getHeader($name): array
    {
        $key = strtolower((string) $name);
        return $this->headerMap[$key] ?? [];
    }

    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->setHeader($name, $value);
        return $clone;
    }

    public function withAddedHeader($name, $value): self
    {
        $clone = clone $this;
        $key = strtolower((string) $name);
        $existing = $clone->headerMap[$key] ?? [];
        $added = $clone->normalizeHeaderValue($value);
        $clone->setHeader($name, array_merge($existing, $added));
        return $clone;
    }

    public function withoutHeader($name): self
    {
        $clone = clone $this;
        $key = strtolower((string) $name);
        unset($clone->headerMap[$key]);
        foreach ($clone->headers as $original => $values) {
            if (strtolower($original) === $key) {
                unset($clone->headers[$original]);
                break;
            }
        }
        return $clone;
    }

    public function withBody(\Psr\Http\Message\StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        $clone = clone $this;
        $clone->statusCode = (int) $code;
        $clone->reasonPhrase = (string) $reasonPhrase;
        return $clone;
    }

    private function setHeaders($headers): void
    {
        $normalized = [];
        if (is_object($headers) && method_exists($headers, 'getAll')) {
            $headers = $headers->getAll();
        }
        if (is_array($headers)) {
            $normalized = $headers;
        }
        foreach ($normalized as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

    private function setHeader(string $name, $value): void
    {
        $values = $this->normalizeHeaderValue($value);
        $this->headers[$name] = $values;
        $this->headerMap[strtolower($name)] = $values;
    }

    private function normalizeHeaderValue($value): array
    {
        if (is_array($value)) {
            return array_values(array_map('strval', $value));
        }
        return [(string) $value];
    }
}

class WordPressHttpStream implements \Psr\Http\Message\StreamInterface
{
    private string $content;
    private int $position = 0;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContents(): string
    {
        $remaining = substr($this->content, $this->position);
        $this->position = strlen($this->content);
        return $remaining;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function close(): void
    {
        $this->detach();
    }

    public function detach()
    {
        $this->content = '';
        $this->position = 0;
        return null;
    }

    public function getSize(): ?int
    {
        return strlen($this->content);
    }

    public function tell(): int
    {
        return $this->position;
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->content);
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $length = strlen($this->content);
        if ($whence === SEEK_SET) {
            $this->position = max(0, (int) $offset);
        } elseif ($whence === SEEK_CUR) {
            $this->position = max(0, $this->position + (int) $offset);
        } elseif ($whence === SEEK_END) {
            $this->position = max(0, $length + (int) $offset);
        }
        if ($this->position > $length) {
            $this->position = $length;
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write($string): int
    {
        $string = (string) $string;
        $before = substr($this->content, 0, $this->position);
        $after = substr($this->content, $this->position + strlen($string));
        $this->content = $before . $string . $after;
        $this->position += strlen($string);
        return strlen($string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read($length): string
    {
        $length = (int) $length;
        $result = substr($this->content, $this->position, $length);
        $this->position += strlen($result);
        return $result;
    }

    public function getMetadata($key = null)
    {
        if ($key === null) {
            return [];
        }
        return null;
    }
}
