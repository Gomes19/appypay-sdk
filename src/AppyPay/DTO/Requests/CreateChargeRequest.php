<?php

namespace AppyPay\DTO\Requests;

class CreateChargeRequest
{
    /**
     * @param array<string,mixed>|null $paymentInfo
     * @param array<string,mixed>|null $notify
     * @param array<string,mixed>|null $metadata
     */
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $merchantTransactionId,
        public readonly string $description,
        public readonly string $paymentMethod,
        public readonly bool $isAsync = false,
        public readonly ?array $paymentInfo = null,
        public readonly ?array $notify = null,
        public readonly ?array $metadata = null
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'merchantTransactionId' => $this->merchantTransactionId,
            'description' => $this->description,
            'paymentMethod' => $this->paymentMethod,
            'isAsync' => $this->isAsync,
        ];

        if ($this->paymentInfo !== null) {
            $payload['paymentInfo'] = $this->paymentInfo;
        }

        if ($this->notify !== null) {
            $payload['notify'] = $this->notify;
        }

        if ($this->metadata !== null) {
            $payload['metadata'] = $this->metadata;
        }

        return $payload;
    }

    /**
     * @param array<string,mixed>|null $notify
     */
    public static function forGpoExpress(
        float $amount,
        string $currency,
        string $merchantTransactionId,
        string $description,
        string $paymentMethod,
        string $phoneNumber,
        ?array $notify = null,
        bool $isAsync = false
    ): self {
        return new self(
            amount: $amount,
            currency: $currency,
            merchantTransactionId: $merchantTransactionId,
            description: $description,
            paymentMethod: $paymentMethod,
            isAsync: $isAsync,
            paymentInfo: ['phoneNumber' => $phoneNumber],
            notify: $notify
        );
    }

}
