<?php

namespace AppyPay\DTO\Responses;

class QrCodeResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $qrCodeArr,
        public readonly ResponseStatus $responseStatus
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (! isset($payload['id'], $payload['qrCodeArr'], $payload['responseStatus'])) {
            throw new \InvalidArgumentException('Invalid QR code response payload.');
        }

        return new self(
            (string) $payload['id'],
            (string) $payload['qrCodeArr'],
            ResponseStatus::fromArray((array) $payload['responseStatus'])
        );
    }
}
