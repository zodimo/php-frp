<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Listeners;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zodimo\FRP\DerivedSignalInterface;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Events\ExternalSignalValueEventInterface;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\Events\RegisterDerivedSignalEvent;
use Zodimo\FRP\Events\RegisterRootSignalEvent;
use Zodimo\FRP\Events\RemoveSignalEvent;
use Zodimo\FRP\Listeners\FrpRuntimeListener;
use Zodimo\FRP\RootSignalInterface;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class FrpRuntimeListenerTest extends TestCase
{
    /**
     * @var MockObject&Runtime
     */
    public Runtime $runtime;

    public FrpRuntimeListener $listener;

    public function setUp(): void
    {
        $this->runtime = $this->createMock(Runtime::class);
        $this->listener = new FrpRuntimeListener($this->runtime);
    }

    public function testCanCreate(): void
    {
        $listener = $this->listener;
        $this->assertInstanceOf(FrpRuntimeListener::class, $listener);
    }

    /**
     *  RegisterRootSignalEvent::class,
     * RegisterDerivedSignalEvent::class,
     * RemoveSignalEvent::class,
     * ExternalSignalValueEventInterface::class,
     * InternalSignalValueEvent::class,.
     */
    public function testCanHandleRegisterRootSignalEvent(): void
    {
        $signalMock = $this->createMock(RootSignalInterface::class);
        $event = RegisterRootSignalEvent::create($signalMock);
        $runtime = $this->runtime;
        $runtime->expects($this->once())->method('registerRootSignal')->with($signalMock);
        $this->listener->process($event);
    }

    public function testCanHandleRegisterDerivedSignalEvent(): void
    {
        $signalMock = $this->createMock(DerivedSignalInterface::class);
        $event = RegisterDerivedSignalEvent::create($signalMock);
        $runtime = $this->runtime;
        $runtime->expects($this->once())->method('registerDerivedSignal')->with($signalMock);
        $this->listener->process($event);
    }

    public function testCanHandleRemoveSignalEvent(): void
    {
        $signalMock = $this->createMock(SignalInterface::class);
        $event = RemoveSignalEvent::create($signalMock);
        $runtime = $this->runtime;
        $runtime->expects($this->once())->method('removeSignal')->with($signalMock);
        $this->listener->process($event);
    }

    public function testCanHandleExternalSignalValueEventInterface(): void
    {
        $mockEvent = $this->createMock(ExternalSignalValueEvent::class);
        $runtime = $this->runtime;
        $runtime->expects($this->once())->method('notifyRootSignals')->with($mockEvent);
        $this->listener->process($mockEvent);
    }

    public function testCanHandleInternalSignalValueEvent(): void
    {
        $mockEvent = $this->createMock(InternalSignalValueEvent::class);
        $runtime = $this->runtime;
        $runtime->expects($this->once())->method('notifyDerivedSignals')->with($mockEvent);
        $this->listener->process($mockEvent);
    }
}
