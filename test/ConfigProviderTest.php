<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $configProvider;

    public function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }

    #[Test]
    public function configIsReturned(): void
    {
        $config = $this->configProvider->__invoke();

        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }
}
