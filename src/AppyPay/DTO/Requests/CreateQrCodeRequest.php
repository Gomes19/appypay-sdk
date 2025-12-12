<?php

namespace AppyPay\DTO\Requests;

use DateTimeInterface;

class CreateQrCodeRequest
{
    /**
     * @param array<string,mixed>|null $notify
     */
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $merchantTransactionId,
        public readonly string $paymentMethod,
        public readonly string $description,
        public readonly string $qrCodeType = 'SINGLE',
        public readonly ?DateTimeInterface $startDate = null,
        public readonly ?DateTimeInterface $endDate = null,
        public readonly ?array $notify = null
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
            'paymentMethod' => $this->paymentMethod,
            'description' => $this->description,
            'qrCodeType' => $this->qrCodeType,
        ];

        if ($this->startDate) {
            $payload['startDate'] = $this->startDate->format(DateTimeInterface::ATOM);
        }

        if ($this->endDate) {
            $payload['endDate'] = $this->endDate->format(DateTimeInterface::ATOM);
        }

        if ($this->notify !== null) {
            $payload['notify'] = $this->notify;
        }

        return $payload;
    }
}
