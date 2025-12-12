<?php

namespace AppyPay\Support;

class AppyPayConfig
{
    public function __construct(
        public readonly string $baseUrl,
        public readonly string $tokenUrl,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $resource,
        public readonly int $timeout = 120,
        public readonly int $connectTimeout = 15,
        public readonly string $acceptLanguage = 'pt-AO',
        public readonly ?string $assertion = null,
        /** @var array<string,string> */
        public readonly array $defaultPaymentMethods = []
    ) {
    }

    /**
     * Convenience helper for building full URLs.
     */
    public function resolveEndpoint(string $endpoint): string
    {
        $base = rtrim($this->baseUrl, '/');
        $path = ltrim($endpoint, '/');

        return $base . '/' . $path;
    }

    public function paymentMethod(string $key, ?string $default = null): ?string
    {
        return $this->defaultPaymentMethods[$key] ?? $default;
    }
}
