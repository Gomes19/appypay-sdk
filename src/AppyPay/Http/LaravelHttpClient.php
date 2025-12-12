<?php

namespace AppyPay\Http;

use AppyPay\Contracts\HttpClientInterface;
use AppyPay\Contracts\TokenProviderInterface;
use AppyPay\Support\AppyPayConfig;
use AppyPay\Support\AppyPayResponse;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class LaravelHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly TokenProviderInterface $tokenProvider,
        private readonly AppyPayConfig $config
    ) {
    }

    /**
     * @param array<string,mixed> $options
     */
    public function request(string $method, string $url, array $options = []): AppyPayResponse
    {
        $token = $this->tokenProvider->getToken();
        $response = $this->performRequest($token, $method, $url, $options);

        if ($response->status() === 401) {
            $token = $this->tokenProvider->refreshToken();
            $response = $this->performRequest($token, $method, $url, $options);
        }

        $json = $response->json();
        $decoded = is_array($json) ? $json : null;

        return new AppyPayResponse(
            $response->status(),
            $response->body(),
            $decoded
        );
    }

    /**
     * @param array<string,mixed> $options
     */
    private function performRequest(string $token, string $method, string $url, array $options): Response
    {
        $pending = $this->buildPendingRequest($token, $options);
        $method = strtolower($method);

        $payload = $options['json'] ?? $options['form_params'] ?? null;

        if (in_array($method, ['get', 'delete'], true)) {
            return $pending->$method($url, $options['query'] ?? []);
        }

        if ($payload !== null) {
            return $pending->$method($url, $payload);
        }

        return $pending->$method($url);
    }

    /**
     * @param array<string,mixed> $options
     */
    private function buildPendingRequest(string $token, array $options): PendingRequest
    {
        $pending = $this->http
            ->timeout($this->config->timeout)
            ->connectTimeout($this->config->connectTimeout)
            ->withToken($token)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Accept-Language' => $this->config->acceptLanguage,
            ]);

        if ($this->config->assertion) {
            $pending = $pending->withHeaders(['Assertion' => $this->config->assertion]);
        }

        if (! empty($options['headers']) && is_array($options['headers'])) {
            $pending = $pending->withHeaders($options['headers']);
        }

        if (! empty($options['query']) && is_array($options['query'])) {
            $pending = $pending->withOptions(['query' => $options['query']]);
        }

        if (! empty($options['form_params']) && is_array($options['form_params'])) {
            $pending = $pending->asForm();
        }

        return $pending;
    }
}
