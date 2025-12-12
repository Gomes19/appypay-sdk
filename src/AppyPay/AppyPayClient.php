<?php

namespace AppyPay;

use AppyPay\Contracts\HttpClientInterface;
use AppyPay\Services\ChargesService;
use AppyPay\Services\QrCodeService;
use AppyPay\Support\AppyPayConfig;

class AppyPayClient
{
    private ?QrCodeService $qrCodeService = null;
    private ?ChargesService $chargesService = null;

    public function __construct(
        private readonly AppyPayConfig $config,
        private readonly HttpClientInterface $httpClient
    ) {
    }

    public function qrCodes(): QrCodeService
    {
        if ($this->qrCodeService === null) {
            $this->qrCodeService = new QrCodeService($this->httpClient, $this->config);
        }

        return $this->qrCodeService;
    }

    public function charges(): ChargesService
    {
        if ($this->chargesService === null) {
            $this->chargesService = new ChargesService($this->httpClient, $this->config);
        }

        return $this->chargesService;
    }
}
