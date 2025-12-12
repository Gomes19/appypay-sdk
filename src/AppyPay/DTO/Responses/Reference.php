<?php

namespace AppyPay\DTO\Responses;

class Reference
{
    public function __construct(
        public readonly ?string $entity = null,
        public readonly ?string $referenceNumber = null
    ) {
    }

    /**
     * @param array<string,mixed>|null $payload
     */
    public static function fromArray(?array $payload): self
    {
        if ($payload === null) {
            return new self();
        }

        return new self(
            isset($payload['entity']) ? (string) $payload['entity'] : null,
            isset($payload['referenceNumber']) ? (string) $payload['referenceNumber'] : null,
        );
    }
}
