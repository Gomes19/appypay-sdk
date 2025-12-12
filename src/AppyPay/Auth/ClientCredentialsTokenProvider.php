<?php

namespace AppyPay\Auth;

use AppyPay\Contracts\TokenProviderInterface;
use AppyPay\Exceptions\AppyPayException;
use AppyPay\Support\AppyPayConfig;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;

class ClientCredentialsTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly CacheRepository $cache,
        private readonly AppyPayConfig $config,
        private readonly string $cacheKey = 'appypay.access_token',
        private readonly int $safetyWindow = 60
    ) {
    }

    public function getToken(): string
    {
        if ($this->cache->has($this->cacheKey)) {
            $cached = $this->cache->get($this->cacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        return $this->refreshToken();
    }

    public function refreshToken(): string
    {
        $this->cache->forget($this->cacheKey);

        $response = $this->http
            ->asForm()
            ->timeout($this->config->timeout)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($this->config->tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->config->clientId,
                'client_secret' => $this->config->clientSecret,
                'resource' => $this->config->resource,
            ]);

        if (! $response->successful()) {
            throw new AppyPayException('Failed to retrieve AppyPay token: ' . $response->body(), $response->status());
        }

        $payload = $response->json();

        if (! is_array($payload) || ! isset($payload['access_token'])) {
            throw new AppyPayException('Invalid token response received from AppyPay.');
        }

        $token = (string) $payload['access_token'];
        $expiresIn = (int) ($payload['expires_in'] ?? 1800);
        $seconds = max(60, $expiresIn - $this->safetyWindow);

        $this->cache->put($this->cacheKey, $token, now()->addSeconds($seconds));

        return $token;
    }
}
