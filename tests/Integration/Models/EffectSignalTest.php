<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Integration\Models;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Models\EffectSignal;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\SignalFactoryService;
use Zodimo\FRP\Tests\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class EffectSignalTest extends TestCase
{
    use MockClosureTrait;

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

    public function testCanCreateEffectSignal(): void
    {
        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $effectConfig = $this->signalConfigFactoryService->createEffectSignalConfig();
        $rootSignal = $this->signalFactoryService->createRootSignal(10, $rootConfig);
        $effectSignal = $this->signalFactoryService->createEffectSignal(fn (int $x) => IOMonad::pure($x + 10), $rootSignal, $effectConfig);
        $this->assertInstanceOf(EffectSignal::class, $effectSignal);
        // assert effect not run on initial value
        $this->assertTrue($effectSignal->getValue()->isNone());
        // signal change
        $rootSignal->setValue(20);
        $this->assertTrue($effectSignal->getValue()->isSome());
        $this->assertEquals(IOMonad::pure(30), $effectSignal->getValue()->unwrap($this->createClosureNotCalled()));
    }

    public function testCanCreateEffectSignalOnSuccess(): void
    {
        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $effectConfig = $this->signalConfigFactoryService->createEffectSignalConfig();
        $onsuccessMock = $this->createClosureMock();
        $onsuccessMock->expects($this->once())->method('__invoke')->with(30)->willReturn(IOMonad::pure(null));
        $effectConfig->setOnSuccess($onsuccessMock);
        $rootSignal = $this->signalFactoryService->createRootSignal(10, $rootConfig);
        $effectSignal = $this->signalFactoryService->createEffectSignal(fn (int $x) => IOMonad::pure($x + 10), $rootSignal, $effectConfig);
        $this->assertInstanceOf(EffectSignal::class, $effectSignal);
        // assert effect not run on initial value
        $this->assertTrue($effectSignal->getValue()->isNone());
        // signal change
        $rootSignal->setValue(20);
        $this->assertTrue($effectSignal->getValue()->isSome());
        $this->assertEquals(IOMonad::pure(30), $effectSignal->getValue()->unwrap($this->createClosureNotCalled()));
    }

    public function testCanCreateWithRunOnInitialValue(): void
    {
        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $effectConfig = $this->signalConfigFactoryService->createEffectSignalConfig();
        $effectConfig = $effectConfig->setRunOnInitialValue(true);
        $onsuccessMock = $this->createClosureMock();
        $onsuccessMock->expects($this->once())->method('__invoke')->with(20)->willReturn(IOMonad::pure(null));
        $effectConfig->setOnSuccess($onsuccessMock);
        $rootSignal = $this->signalFactoryService->createRootSignal(10, $rootConfig);
        $effectSignal = $this->signalFactoryService->createEffectSignal(fn (int $x) => IOMonad::pure($x + 10), $rootSignal, $effectConfig);
        $this->assertInstanceOf(EffectSignal::class, $effectSignal);
        $this->assertTrue($effectSignal->getValue()->isSome());
        $this->assertEquals(IOMonad::pure(20), $effectSignal->getValue()->unwrap($this->createClosureNotCalled()));
    }
}
