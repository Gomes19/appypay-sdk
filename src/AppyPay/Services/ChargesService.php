<?php

namespace AppyPay\Services;

use AppyPay\Contracts\HttpClientInterface;
use AppyPay\DTO\Requests\CreateChargeRequest;
use AppyPay\DTO\Responses\ChargeResponse;
use AppyPay\Exceptions\AppyPayException;
use AppyPay\Support\AppyPayConfig;
use AppyPay\Support\AppyPayResponse;
use InvalidArgumentException;

class ChargesService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AppyPayConfig $config
    ) {
    }

    public function create(CreateChargeRequest $request): ChargeResponse
    {
        $response = $this->httpClient->request('POST', $this->config->resolveEndpoint('charges'), [
            'json' => $request->toArray(),
        ]);

        if (! $response->successful()) {
            throw new AppyPayException('Failed to create charge: ' . $response->body(), $response->status());
        }

        return ChargeResponse::fromArray($response->json());
    }

    /**
     * @param array<string,mixed>|null $notify
     */
    public function createGpoPayment(
        float $amount,
        string $currency,
        string $merchantTransactionId,
        string $description,
        string $phoneNumber,
        ?array $notify = null,
        bool $isAsync = false,
        ?string $paymentMethod = null
    ): ChargeResponse {
        $method = $paymentMethod ?? $this->config->paymentMethod('gpo_express');

        if ($method === null) {
            throw new InvalidArgumentException('GPO Express payment method is not configured.');
        }

        $request = CreateChargeRequest::forGpoExpress(
            amount: $amount,
            currency: $currency,
            merchantTransactionId: $merchantTransactionId,
            description: $description,
            paymentMethod: $method,
            phoneNumber: $phoneNumber,
            notify: $notify,
            isAsync: $isAsync
        );

        return $this->create($request);
    }

    public function find(string $chargeId): ChargeResponse
    {
        $response = $this->httpClient->request('GET', $this->config->resolveEndpoint("charges/{$chargeId}"));

        if (! $response->successful()) {
            throw new AppyPayException('Failed to retrieve charge: ' . $response->body(), $response->status());
        }

        return ChargeResponse::fromArray($response->json());
    }

    /**
     * @param array<string,mixed> $filters
     */
    public function list(array $filters = []): AppyPayResponse
    {
        $response = $this->httpClient->request('GET', $this->config->resolveEndpoint('charges'), [
            'query' => $filters,
        ]);

        if (! $response->successful()) {
            throw new AppyPayException('Failed to list charges: ' . $response->body(), $response->status());
        }

        return $response;
    }
}
