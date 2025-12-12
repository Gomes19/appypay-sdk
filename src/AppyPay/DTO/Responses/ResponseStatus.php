<?php

namespace AppyPay\DTO\Responses;

class ResponseStatus
{
    public function __construct(
        public readonly bool $successful,
        public readonly string $status,
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?string $source = null,
        public readonly ?Reference $reference = null
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            (bool) ($payload['successful'] ?? false),
            (string) ($payload['status'] ?? ''),
            isset($payload['code']) ? (int) $payload['code'] : null,
            isset($payload['message']) ? (string) $payload['message'] : null,
            isset($payload['source']) ? (string) $payload['source'] : null,
            Reference::fromArray(isset($payload['reference']) && is_array($payload['reference']) ? $payload['reference'] : null),
        );
    }
}
