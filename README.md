# Health Checks Bundle

This bundle integrates

----

## Prerequisites
* [php](https://www.php.net/releases/8.1/en.php) - ^8.1
* [symfony](https://symfony.com/doc/6.1/index.html) - ^6.1

----

## Installation

Install the latest version with composer:

```bash
$ composer require ringostat/health-checks-bundle
```

Then register the bundle in the `config/bundles.php` file:

```php
return [
    // ...
    HealthChecksBundle\RingostatHealthChecksBundle::class => ['all' => true],
    // ...
];
```

And finally add the following to your `config/routes/health_checks.yaml`:

```yaml
health_check:
    resource: '@HealthChecksBundle/config/routing.yaml'
```

## Enabling built-in health checks

```yaml
health_checks:
    health_checks:
        - id: health_checks.mongodb_connection_check
        - id: health_checks.mongodb_select_check
    ping_checks:
        - { service: 'service_name', endpoint: 'https://service.ringostat.com/ping' }
```

## Add custom check

```php
<?php

declare(strict_types=1);

namespace App\Service\Check;

use HealthChecksBundle\Check\CheckInterface;
use HealthChecksBundle\Dto\Response;
use HealthChecksBundle\Enum\Status;

class CustomCheck implements CheckInterface
{
    private const CHECK_NAME = 'check_name';
    private const CHECK_DESCRIPTION = 'Check description';

    public function check(): Response
    {
        //...
        
        return new Response(
            self::CHECK_NAME,
            Status::PASS,
            self::CHECK_DESCRIPTION,
            $duration
        );
    }
}```
