<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturn\Option;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\Models\RootSignal;
use Zodimo\FRP\Models\RootSignalConfig;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\Tests\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class RootSignalTest extends TestCase
{
    use FrpTestingEnvironmentFactoryTrait;

    /**
     * @var RootSignalConfig<mixed,mixed>
     */
    public RootSignalConfig $signalConfig;

    public function setUp(): void
    {
        $frpEnv = $this->createFrpTestEnvironment();
        $this->signalConfig = SignalConfigFactoryService::create($frpEnv->container)->createRootSignalConfig();
    }

    /**
     * @return RootSignalConfig<mixed,mixed>
     */
    public function createRootSignalConfigWithName(string $name): RootSignalConfig
    {
        $frpEnv = $this->createFrpTestEnvironment();

        return SignalConfigFactoryService::create($frpEnv->container)->createRootSignalConfig($name);
    }

    public function testCanCreate(): void
    {
        $id = 'some-id';
        $signal = RootSignal::create($id, 10, $this->signalConfig);
        $this->assertInstanceOf(RootSignal::class, $signal);
        $this->assertEquals(10, $signal->getValue());
        $this->assertInstanceOf(Option::class, $signal->getTimestamp());
    }

    public function testCanReceiveNewValue(): void
    {
        $id = 'some-id';

        $defaulSignaltValue = 10;
        $newSignaltValue = 20;

        $externalClassName = ExternalSignalValueEvent::class;

        $timestamp = new \DateTimeImmutable();

        $externSignalEvent = ExternalSignalValueEvent::create($id, $timestamp, $newSignaltValue, $externalClassName);

        $signal = RootSignal::create($id, $defaulSignaltValue, $this->signalConfig);
        // before update
        $this->assertEquals($defaulSignaltValue, $signal->getValue());
        $this->assertEquals(Option::none(), $signal->getTimestamp());
        // after update
        $signal->receive($externSignalEvent);

        $this->assertEquals($newSignaltValue, $signal->getValue());
        $this->assertEquals(Option::some($timestamp), $signal->getTimestamp());
    }

    public function testSendsInternalSignalEventForUnchanged(): void
    {
        $id = 'some-id';

        $defaulSignaltValue = 10;
        $newSignaltValue = 10;

        $externalClassName = ExternalSignalValueEvent::class;

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $signal = RootSignal::create($id, $defaulSignaltValue, $this->signalConfig->setEventDispatcher($mockEventDispatcher));

        $timestamp = new \DateTimeImmutable();

        $externSignalEvent = ExternalSignalValueEvent::create($id, $timestamp, $newSignaltValue, $externalClassName);
        $expectedInternalSignalEvent = InternalSignalValueEvent::create($signal->getId(), $timestamp, false);
        $mockEventDispatcher->expects($this->once())->method('dispatch')->with($expectedInternalSignalEvent);

        // before update
        $this->assertEquals($defaulSignaltValue, $signal->getValue());
        $this->assertEquals(Option::none(), $signal->getTimestamp());
        // after update
        $signal->receive($externSignalEvent);
    }

    public function testSendsInternalSignalEventForChanged(): void
    {
        $id = 'some-id';

        $defaulSignaltValue = 10;
        $newSignaltValue = 20;

        $externalClassName = ExternalSignalValueEvent::class;

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $signal = RootSignal::create($id, $defaulSignaltValue, $this->signalConfig->setEventDispatcher($mockEventDispatcher));

        $timestamp = new \DateTimeImmutable();

        $externSignalEvent = ExternalSignalValueEvent::create($id, $timestamp, $newSignaltValue, $externalClassName);
        $expectedInternalSignalEvent = InternalSignalValueEvent::create($signal->getId(), $timestamp, true);
        $mockEventDispatcher->expects($this->once())->method('dispatch')->with($expectedInternalSignalEvent);

        // before update
        $this->assertEquals($defaulSignaltValue, $signal->getValue());
        $this->assertEquals(Option::none(), $signal->getTimestamp());
        // after update
        $signal->receive($externSignalEvent);
    }

    public function testCanSetValueOnSignal(): void
    {
        $id = 'some-id';

        $defaulSignaltValue = 10;
        $newSignaltValue = 20;

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $signal = RootSignal::create($id, $defaulSignaltValue, $this->signalConfig->setEventDispatcher($mockEventDispatcher));

        $timestamp = new \DateTimeImmutable();

        $expectedInternalSignalEvent = InternalSignalValueEvent::create($signal->getId(), $timestamp, true);
        $mockEventDispatcher->expects($this->once())->method('dispatch')->with($expectedInternalSignalEvent);

        // before update
        $this->assertEquals($defaulSignaltValue, $signal->getValue());
        $this->assertEquals(Option::none(), $signal->getTimestamp());
        // after update
        $signal->setValue($newSignaltValue, $timestamp);
    }

    public function testGetNameOrIdWithName(): void
    {
        $id = '124';
        $name = 'some-name';
        $signalConfig = $this->createRootSignalConfigWithName($name);
        $signal = RootSignal::create($id, 10, $signalConfig);
        $this->assertEquals($name, $signal->getNameOrId());
    }
}
