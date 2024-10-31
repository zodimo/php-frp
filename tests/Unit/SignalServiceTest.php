<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\FRP\DerivedSignalInterface;
use Zodimo\FRP\RootSignalInterface;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\SignalFactoryService;
use Zodimo\FRP\SignalService;

/**
 * @internal
 *
 * @coversNothing
 */
class SignalServiceTest extends TestCase
{
    /**
     * @var ContainerInterface&MockObject
     */
    public ContainerInterface $container;

    public Runtime $runtime;

    public SignalConfigFactoryService $configFactoryService;

    public SignalFactoryService $factoryService;

    public SignalService $signalService;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->container->method('get')->with(EventDispatcherInterface::class)->willReturn($eventDispatcher);
        $this->runtime = new Runtime();
        $this->configFactoryService = new SignalConfigFactoryService($this->container);
        $this->factoryService = new SignalFactoryService($this->runtime);
        $this->signalService = new SignalService($this->factoryService, $this->configFactoryService, $this->runtime);
    }

    public function testCanCreate(): void
    {
        $service = $this->signalService;
        $this->assertInstanceOf(SignalService::class, $service);
    }

    public function testCanCreateConstSignal(): void
    {
        $signal = $this->signalService->const(10);
        $this->assertInstanceOf(RootSignalInterface::class, $signal);
        $this->assertEquals(10, $signal->getValue());
    }

    public function testcreateDerievedSignalFromLift(): void
    {
        $const1 = 10;
        $signal1 = $this->signalService->const($const1);

        $s2func = fn (int $x) => $x + 10;
        $signal2 = $this->signalService->lift($s2func, $signal1);

        $this->assertInstanceOf(DerivedSignalInterface::class, $signal2);
        $this->assertEquals($s2func($const1), $signal2->getValue());
    }

    public function testcreateDerievedSignalFromLift2AndHigherOrder(): void
    {
        $const1 = 10;
        $signal1 = $this->signalService->const($const1);

        $s2func = fn (int $x) => $x + 10;
        $signal2 = $this->signalService->lift($s2func, $signal1);

        $s3func = fn (int $x, int $y) => $x + $y;
        $signal3 = $this->signalService->lift2($s3func, $signal1, $signal2);

        $this->assertInstanceOf(DerivedSignalInterface::class, $signal2);
        $this->assertEquals($s2func($const1), $signal2->getValue());

        $this->assertInstanceOf(DerivedSignalInterface::class, $signal3);
        $this->assertEquals($s3func($signal1->getValue(), $signal2->getValue()), $signal3->getValue());
    }

    public function testSignalCreateRootSignalWithConfigBuilder(): void
    {
        $signalName = 'some-signal-name';
        $signal = $this->signalService->createRootSignalWithConfigBuilder(10, function ($builder) use ($signalName) {
            $builder->setSignalName($signalName);

            return $builder;
        });
        $this->assertInstanceOf(RootSignalInterface::class, $signal);
        $this->assertEquals($signalName, $signal->getNameOrId());
    }
}
