<?php

namespace AppyPay\Contracts;

use AppyPay\Support\AppyPayResponse;

interface HttpClientInterface
{
    /**
     * Perform an HTTP request against AppyPay.
     *
     * @param array<string,mixed> $options
     */
    public function request(string $method, string $url, array $options = []): AppyPayResponse;
}
