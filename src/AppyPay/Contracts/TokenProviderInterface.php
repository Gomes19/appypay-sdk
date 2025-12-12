<?php

namespace AppyPay\Contracts;

interface TokenProviderInterface
{
    /**
     * Retrieve a valid access token for AppyPay requests.
     */
    public function getToken(): string;

    /**
     * Invalidate the cached token and return a fresh one.
     */
    public function refreshToken(): string;
}
