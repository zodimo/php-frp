<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Integration\Models;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Zodimo\FRP\Models\DerivedSignal;
use Zodimo\FRP\RootSignalInterface;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\SignalFactoryService;
use Zodimo\FRP\Tests\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class DerivedSignalTest extends TestCase
{
    use FrpTestingEnvironmentFactoryTrait;

    public ListenerProviderInterface $listenerProvider;

    /**
     * @var EventDispatcherInterface&MockObject
     */
    public EventDispatcherInterface $eventDispatcher;

    /**
     * @var ContainerInterface&MockObject
     */
    public ContainerInterface $container;

    public Runtime $runtime;

    public SignalFactoryService $signalFactoryService;

    public SignalConfigFactoryService $signalConfigFactoryService;

    public function setUp(): void
    {
        $frpEnv = $this->createFrpTestEnvironment();

        $this->runtime = $frpEnv->runtime;
        $this->signalConfigFactoryService = SignalConfigFactoryService::create($frpEnv->container);
        $this->signalFactoryService = SignalFactoryService::create($this->runtime);
    }

    public function testCanCreateRootSignal(): void
    {
        $config = $this->signalConfigFactoryService->createRootSignalConfig();
        $signal = $this->signalFactoryService->createRootSignal(10, $config);
        $this->assertInstanceOf(RootSignalInterface::class, $signal);
    }

    public function testCanCreateDerivedSignal(): void
    {
        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $derivedConfig = $this->signalConfigFactoryService->createDerivedSignalConfig();
        $rootSignal = $this->signalFactoryService->createRootSignal(10, $rootConfig);
        $derivedSignal = $this->signalFactoryService->createDerivedSignal(fn (int $x) => $x + 10, [$rootSignal], $derivedConfig);
        $this->assertInstanceOf(DerivedSignal::class, $derivedSignal);
    }
}
