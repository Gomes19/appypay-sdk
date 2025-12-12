<?php

namespace AppyPay\Services;

use AppyPay\Contracts\HttpClientInterface;
use AppyPay\DTO\Requests\CreateQrCodeRequest;
use AppyPay\DTO\Responses\QrCodeResponse;
use AppyPay\Exceptions\AppyPayException;
use AppyPay\Support\AppyPayConfig;

class QrCodeService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AppyPayConfig $config
    ) {
    }

    public function create(CreateQrCodeRequest $request): QrCodeResponse
    {
        $response = $this->httpClient->request('POST', $this->config->resolveEndpoint('qr-codes'), [
            'json' => $request->toArray(),
        ]);

        if (! $response->successful()) {
            throw new AppyPayException('Failed to create QR code: ' . $response->body(), $response->status());
        }

        return QrCodeResponse::fromArray($response->json());
    }
}
