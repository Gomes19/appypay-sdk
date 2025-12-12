# AppyPay SDK (Laravel)

SDK não-oficial para integrar aplicações Laravel com o gateway de pagamentos angolano AppyPay. Fornece serviços para emissão de QR Codes, cobranças via Multicaixa Express (GPO) e referências, abstracção de autenticação OAuth2 e modelos de resposta tipados.

## Funcionalidades

- Autenticação `client_credentials` com cache e refresh automático.
- Cliente HTTP partilhado com renovação transparente de token em `401`.
- Serviços para:
  - Criar/listar charges.
  - Criar cobranças GPO (push para Multicaixa Express).
  - Criar cobranças por referência (REF).
  - Emitir QR Codes.
- DTOs para requests e responses, com exceções customizadas (`AppyPayException`).

## Instalação

### Usando Composer (path repository)

Para testar a SDK num outro projecto Laravel sem publicá-la no Packagist, adiciona um *path repository* ao `composer.json` desse projecto:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../appypay-sdk"
    }
  ]
}
```

A seguir instala a dependência:

```bash
composer require gomesmateus/appypay-sdk:^1.0.2
```

> Ajusta o caminho em `url` para apontar para a pasta onde o SDK está clonado.

### Directamente neste repositório

Se quiseres apenas experimentar localmente, garante que as dependências do Laravel (HTTP e Cache) estão disponíveis através do teu projecto hospedeiro.

## Configuração

Publica o ficheiro de configuração (opcional, mas recomendado):

```bash
php artisan vendor:publish --tag=appypay-config
```

Define as variáveis no `.env` do projecto que consome o SDK:

```
APPYPAY_BASE_URL=https://gwy-api.appypay.co.ao/v2.0
APPYPAY_TOKEN_URL=https://auth.appypay.co.ao/connect/token
APPYPAY_CLIENT_ID=xxxxxxxx
APPYPAY_CLIENT_SECRET=xxxxxxxx
APPYPAY_RESOURCE=xxxx
APPYPAY_PAYMENT_METHOD_GPO_QR=GPO_xxx
APPYPAY_PAYMENT_METHOD_GPO_EXPRESS=GPO_xxx
APPYPAY_PAYMENT_METHOD_REFERENCE=REF_xxx
```

> Verifica na documentação oficial quais IDs de métodos de pagamento correspondem ao teu ambiente (produção/sandbox).

Depois limpa o cache de configuração, se necessário:

```bash
php artisan config:clear
```

## Utilização

Resolve o cliente através do container:

```php
use AppyPay\AppyPayClient;

$client = app(AppyPayClient::class);
```

### Criar QR Code

```php
use AppyPay\DTO\Requests\CreateQrCodeRequest;

$response = $client->qrCodes()->create(new CreateQrCodeRequest(
  amount: 2500.00,
  currency: 'AOA',
  merchantTransactionId: 'TX-' . uniqid(),
  paymentMethod: config('appypay.payment_methods.gpo_qr'),
  description: 'Pagamento de teste via QR Code',
  qrCodeType: 'SINGLE',
  startDate: new \DateTime('2025-01-01 10:00:00'),
  endDate: new \DateTime('2025-01-01 18:00:00')
));

$qrCodeBase64 = $response->qrCodeArr;
```

### Multicaixa Express (GPO)

```php
$charge = $client->charges()->createGpoPayment(
  amount: 2500.00,
  currency: 'AOA',
  merchantTransactionId: 'TX-' . uniqid(),
  description: 'Consulta de teste',
  phoneNumber: '244923000000',
  notify: [
    'name' => 'Cliente Teste',
    'telephone' => '244923000000',
    'email' => 'cliente@example.com',
    'smsNotification' => true,
    'emailNotification' => false,
  ]
);

$referenceNumber = $charge->reference()?->referenceNumber;
```

### Consultar charge

```php
$charge = $client->charges()->find($chargeId);
$status = $charge->responseStatus->status;
```

### Referência (REF)

```php
use AppyPay\DTO\Requests\CreateChargeRequest;

$charge = $client->charges()->create(new CreateChargeRequest(
    amount: 2500.00,
    currency: 'AOA',
    merchantTransactionId: 'TX-' . uniqid(),
    description: 'Pagamento por referência',
    paymentMethod: config('appypay.payment_methods.ref'),
    isAsync: true
));

$referenceNumber = $charge->reference()?->referenceNumber;
```

### Listar charges

```php
$response = $client->charges()->list([
    'status' => 'Pending',
]);

$payload = $response->json();
```

## Testes Locais

- Usa `php artisan tinker` para executar chamadas rápidas, garantindo que as credenciais são válidas.
- Para testes automatizados, usa `Illuminate\Support\Facades\Http::fake()` e simula respostas (`401` seguido de `200` para validar refresh do token, `422` para erros de validação, etc.).
- Se tiveres ambiente sandbox da AppyPay, cria um `.env.testing` com credenciais próprias e defines `APP_ENV=testing` para executar testes de integração reais.

## Roadmap

- Webhook helper (validação e normalização de payloads).
- Suporte a outros endpoints (refunds, void, status async).
- Transformers opcionais para responses para frameworks não Laravel.

## Licença

MIT © Gomes Mateus
