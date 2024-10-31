<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Integration\Models;

use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Models\RootSignalConfig;
use Zodimo\FRP\Models\RootSignalConfigBuilder;

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

    public function testSetGetCompareFunction(): void
    {
        $compareFunc = function (int $x, int $y): bool { return $x == $y; };
        $builder = RootSignalConfigBuilder::create();
        $builder->setCompareFunction($compareFunc);

        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertSame($compareFunc, $config->getCompareFunction());
    }

    public function testSetGetEventDispatcher(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $builder = RootSignalConfigBuilder::create();
        $builder->setEventDispatcher($eventDispatcher);

        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertSame($eventDispatcher, $config->getEventDispatcher());
    }

    public function testSetGetSignalName(): void
    {
        $signalName = 'some-name';
        $builder = RootSignalConfigBuilder::create();
        $builder->setSignalName($signalName);

        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertTrue($config->getSignalName()->isSome());
        $this->assertEquals($signalName, $config->getSignalName()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetExternalEventClass(): void
    {
        $exteralEventClass = ExternalSignalValueEvent::class;
        $builder = RootSignalConfigBuilder::create();

        $builder->setExteralEventClass($exteralEventClass);
        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertTrue($config->getExteralEventClass()->isSome());
        $this->assertSame($exteralEventClass, $config->getExteralEventClass()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetFilter(): void
    {
        $filter = function (int $x): bool { return 1 == $x; };
        $builder = RootSignalConfigBuilder::create();

        $builder->setFilter($filter);
        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertTrue($config->getFilter()->isSome());
        $this->assertSame($filter, $config->getFilter()->unwrap($this->createClosureNotCalled()));
    }

    public function testSetGetClock(): void
    {
        $clock = $this->createMock(ClockInterface::class);
        $builder = RootSignalConfigBuilder::create();

        $builder->setClock($clock);
        $config = RootSignalConfig::createFromBuilder($builder, $this->eventDispatcher);
        $this->assertSame($clock, $config->getClock());
    }
}
