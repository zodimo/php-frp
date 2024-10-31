<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Models\RootSignalConfigBuilder;

/**
 * @internal
 *
 * @coversNothing
 */
class RootSignalConfigBuilderTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $builder = RootSignalConfigBuilder::create();
        $this->assertInstanceOf(RootSignalConfigBuilder::class, $builder);
    }

    public function testSetGetCompareFunction(): void
    {
        $compareFunc = function (int $x, int $y): bool { return $x == $y; };
        $builder = RootSignalConfigBuilder::create();
        $builder->setCompareFunction($compareFunc);
        $this->assertTrue($builder->getCompareFunction()->isSome());
        $this->assertSame($compareFunc, $builder->getCompareFunction()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetEventDispatcher(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $builder = RootSignalConfigBuilder::create();
        $builder->setEventDispatcher($eventDispatcher);
        $this->assertTrue($builder->getEventDispatcher()->isSome());
        $this->assertSame($eventDispatcher, $builder->getEventDispatcher()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetSignalName(): void
    {
        $signalName = 'some-name';
        $builder = RootSignalConfigBuilder::create();
        $builder->setSignalName($signalName);
        $this->assertTrue($builder->getSignalName()->isSome());
        $this->assertSame($signalName, $builder->getSignalName()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetExternalEventClass(): void
    {
        $exteralEventClass = ExternalSignalValueEvent::class;
        $builder = RootSignalConfigBuilder::create();

        $builder->setExteralEventClass($exteralEventClass);
        $this->assertTrue($builder->getExteralEventClass()->isSome());
        $this->assertSame($exteralEventClass, $builder->getExteralEventClass()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetFilter(): void
    {
        $filter = function (int $x): bool { return 1 == $x; };
        $builder = RootSignalConfigBuilder::create();

        $builder->setFilter($filter);
        $this->assertTrue($builder->getFilter()->isSome());
        $this->assertSame($filter, $builder->getFilter()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetClock(): void
    {
        $clock = $this->createMock(ClockInterface::class);
        $builder = RootSignalConfigBuilder::create();

        $builder->setClock($clock);
        $this->assertTrue($builder->getClock()->isSome());
        $this->assertSame($clock, $builder->getClock()->unwrap($this->createClosureNotCalled()));
    }
}
