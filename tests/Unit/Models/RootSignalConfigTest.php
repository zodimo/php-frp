<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Models\RootSignalConfig;

/**
 * @internal
 *
 * @coversNothing
 */
class RootSignalConfigTest extends TestCase
{
    use MockClosureTrait;

    private EventDispatcherInterface $eventDispatcher;

    public function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    public function testCanCreate(): void
    {
        $config = RootSignalConfig::create($this->eventDispatcher);
        $this->assertInstanceOf(RootSignalConfig::class, $config);
        $this->assertSame($this->eventDispatcher, $config->getEventDispatcher());
        $this->assertTrue($config->getSignalName()->isNone());
        $this->assertIsCallable($config->getCompareFunction());
    }

    public function testCanCreateWithName(): void
    {
        $name = 'signal-name';
        $config = RootSignalConfig::create($this->eventDispatcher, $name);
        $this->assertTrue($config->getSignalName()->isSome());
        $this->assertEquals($name, $config->getSignalName()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetCompareFunction(): void
    {
        $compareFunc = function (int $x, int $y): bool { return $x == $y; };
        $config = RootSignalConfig::create($this->eventDispatcher);
        $config->setCompareFunction($compareFunc);
        $this->assertSame($compareFunc, $config->getCompareFunction());
    }

    public function testSetGetEventDispatcher(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $config = RootSignalConfig::create($this->eventDispatcher);
        $config->setEventDispatcher($eventDispatcher);
        $this->assertSame($eventDispatcher, $config->getEventDispatcher());
    }

    public function testSetGetExternalEventClass(): void
    {
        $exteralEventClass = ExternalSignalValueEvent::class;
        $config = RootSignalConfig::create($this->eventDispatcher);

        $config->setExteralEventClass($exteralEventClass);
        $this->assertTrue($config->getExteralEventClass()->isSome());
        $this->assertSame($exteralEventClass, $config->getExteralEventClass()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetFilter(): void
    {
        $filter = function (int $x): bool { return 1 == $x; };
        $config = RootSignalConfig::create($this->eventDispatcher);

        $config->setFilter($filter);
        $this->assertTrue($config->getFilter()->isSome());
        $this->assertSame($filter, $config->getFilter()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetClock(): void
    {
        $clock = $this->createMock(ClockInterface::class);
        $config = RootSignalConfig::create($this->eventDispatcher);

        $config->setClock($clock);
        $this->assertSame($clock, $config->getClock());
    }
}
