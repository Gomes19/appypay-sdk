<?php

namespace AppyPay\DTO\Responses;

class ChargeResponse
{
    /**
     * @param array<string,mixed>|null $raw
     */
    public function __construct(
        public readonly string $id,
        public readonly string $merchantTransactionId,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $paymentMethod,
        public readonly ResponseStatus $responseStatus,
        public readonly ?array $raw = null
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (! isset($payload['id'], $payload['responseStatus'])) {
            throw new \InvalidArgumentException('Invalid charge response payload.');
        }

        return new self(
            (string) $payload['id'],
            (string) ($payload['merchantTransactionId'] ?? ''),
            isset($payload['amount']) ? (float) $payload['amount'] : 0.0,
            (string) ($payload['currency'] ?? ''),
            (string) ($payload['paymentMethod'] ?? ''),
            ResponseStatus::fromArray((array) $payload['responseStatus']),
            $payload
        );
    }

    public function reference(): ?Reference
    {
        return $this->responseStatus->reference;
    }
}
