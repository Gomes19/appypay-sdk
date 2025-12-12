<?php

namespace AppyPay;

use AppyPay\Auth\ClientCredentialsTokenProvider;
use AppyPay\Contracts\HttpClientInterface;
use AppyPay\Contracts\TokenProviderInterface;
use AppyPay\Http\LaravelHttpClient;
use AppyPay\Support\AppyPayConfig;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

class AppyPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'appypay');

        $this->app->singleton(AppyPayConfig::class, function (Container $app): AppyPayConfig {
            $config = $app['config']->get('appypay', []);

            return new AppyPayConfig(
                baseUrl: (string) ($config['base_url'] ?? ''),
                tokenUrl: (string) ($config['token_url'] ?? ''),
                clientId: (string) ($config['client_id'] ?? ''),
                clientSecret: (string) ($config['client_secret'] ?? ''),
                resource: (string) ($config['resource'] ?? ''),
                timeout: (int) ($config['timeout'] ?? 120),
                connectTimeout: (int) ($config['connect_timeout'] ?? 15),
                acceptLanguage: (string) ($config['accept_language'] ?? 'pt-AO'),
                assertion: isset($config['assertion']) ? (string) $config['assertion'] : null,
                defaultPaymentMethods: isset($config['payment_methods']) && is_array($config['payment_methods'])
                    ? $config['payment_methods']
                    : []
            );
        });

        $this->app->singleton(TokenProviderInterface::class, function (Container $app): TokenProviderInterface {
            return new ClientCredentialsTokenProvider(
                http: $app->make(HttpFactory::class),
                cache: $app->make(CacheRepository::class),
                config: $app->make(AppyPayConfig::class)
            );
        });

        $this->app->singleton(HttpClientInterface::class, function (Container $app): HttpClientInterface {
            return new LaravelHttpClient(
                http: $app->make(HttpFactory::class),
                tokenProvider: $app->make(TokenProviderInterface::class),
                config: $app->make(AppyPayConfig::class)
            );
        });

        $this->app->singleton(AppyPayClient::class, function (Container $app): AppyPayClient {
            return new AppyPayClient(
                config: $app->make(AppyPayConfig::class),
                httpClient: $app->make(HttpClientInterface::class)
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path('appypay.php'),
            ], 'appypay-config');
        }
    }

    private function configPath(): string
    {
        return __DIR__ . '/../../config/appypay.php';
    }
}
