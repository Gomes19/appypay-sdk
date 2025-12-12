<?php

namespace AppyPay\Support;

use AppyPay\Exceptions\AppyPayException;

class AppyPayResponse
{
    /** @var array<string,mixed>|null */
    private ?array $json;

    public function __construct(
        private readonly int $status,
        private readonly string $body,
        ?array $json
    ) {
        $this->json = $json;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return array<string,mixed>
     * @throws AppyPayException
     */
    public function json(): array
    {
        if ($this->json === null) {
            throw new AppyPayException('Response body is not JSON.');
        }

        return $this->json;
    }

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
