<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\FRP\Models\RootSignalConfig;
use Zodimo\FRP\SignalConfigFactoryService;

/**
 * @internal
 *
 * @coversNothing
 */
class SignalConfigFactoryServiceTest extends TestCase
{
    public SignalConfigFactoryService $signalConfigFactoryService;

    public function setUp(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(EventDispatcherInterface::class)->willReturn($eventDispatcher);
        $this->signalConfigFactoryService = new SignalConfigFactoryService($container);
    }

    public function testCanCreate(): void
    {
        $service = $this->signalConfigFactoryService;
        $this->assertInstanceOf(SignalConfigFactoryService::class, $service);
    }

    public function testCanCreateRootSignalConfig(): void
    {
        $config = $this->signalConfigFactoryService->createRootSignalConfig();
        $this->assertInstanceOf(RootSignalConfig::class, $config);
    }
}
