<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturn\Option;

/**
 * @template TVALUE
 * @template TEXTERNAL_EVENT
 */
class RootSignalConfig
{
    /**
     * @var callable(TVALUE,TVALUE):bool
     */
    private $compare;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var Option<string>
     */
    private Option $signalNameOption;

    /**
     * @var Option<class-string<TEXTERNAL_EVENT>>
     */
    private Option $externalEventClassOption;

    /**
     * @var Option<callable(TVALUE):bool>
     */
    private Option $filter;
    private ClockInterface $clock;

    /**
     * @param callable(TVALUE,TVALUE):bool          $compare
     * @param Option<string>                        $signalNameOption
     * @param Option<class-string<TEXTERNAL_EVENT>> $externalEventClassOption
     * @param Option<callable(TVALUE):bool>         $filter
     */
    private function __construct(
        $compare,
        EventDispatcherInterface $eventDispatcher,
        Option $signalNameOption,
        Option $externalEventClassOption,
        Option $filter,
        ClockInterface $clock
    ) {
        $this->compare = $compare;
        $this->eventDispatcher = $eventDispatcher;
        $this->signalNameOption = $signalNameOption;
        $this->externalEventClassOption = $externalEventClassOption;
        $this->filter = $filter;
        $this->clock = $clock;
    }

    /**
     * @template _TVALUE
     * @template _TEXTERNAL_EVENT
     *
     * @param RootSignalConfigBuilder<_TVALUE,_TEXTERNAL_EVENT> $builder
     *
     * @return RootSignalConfig<_TVALUE,_TEXTERNAL_EVENT>
     */
    public static function createFromBuilder(RootSignalConfigBuilder $builder, EventDispatcherInterface $defaultEventDispatcher)
    {
        $config = self::create(
            $builder->getEventDispatcher()->match(
                fn ($eventDispatcher) => $eventDispatcher,
                fn () => $defaultEventDispatcher
            ),
            $builder->getSignalName()->match(
                fn ($signalName) => $signalName,
                fn () => null
            )
        );
        $config = $builder->getCompareFunction()->match(
            fn ($compare) => $config->setCompareFunction($compare),
            fn () => $config
        );

        $config = $builder->getFilter()->match(
            fn ($filter) => $config->setFilter($filter),
            fn () => $config
        );

        $config = $builder->getExteralEventClass()->match(
            fn ($exteralEventClass) => $config->setExteralEventClass($exteralEventClass),
            fn () => $config
        );

        return $builder->getClock()->match(
            fn ($clock) => $config->setClock($clock),
            fn () => $config
        );
    }

    /**
     * @return RootSignalConfig<mixed,mixed>
     */
    public static function create(EventDispatcherInterface $eventDispatcher, ?string $signalName = null): RootSignalConfig
    {
        $defaultCompare = fn ($x, $y): bool => $x === $y;
        if (is_null($signalName)) {
            $signalNameOption = Option::none();
        } else {
            $signalNameOption = Option::some($signalName);
        }

        $clock = new class implements ClockInterface {
            public function now(): \DateTimeImmutable
            {
                return new \DateTimeImmutable();
            }
        };

        return new self(
            $defaultCompare,
            $eventDispatcher,
            $signalNameOption,
            Option::none(),
            Option::none(),
            $clock
        );
    }

    // ///////////////////////
    // compare
    // //////////////////////
    /**
     * @return callable(TVALUE,TVALUE):bool
     */
    public function getCompareFunction(): callable
    {
        return $this->compare;
    }

    /**
     * @template _TVALUE
     *
     * @param callable(_TVALUE,_TVALUE):bool $func
     *
     * @return RootSignalConfig<_TVALUE,TEXTERNAL_EVENT>
     */
    public function setCompareFunction(callable $func): RootSignalConfig
    {
        $this->compare = $func;

        return $this;
    }

    // ///////////////////////
    // EVENT DISPATCHER
    // //////////////////////
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @return RootSignalConfig<TVALUE,TEXTERNAL_EVENT>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): RootSignalConfig
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    // ///////////////////////
    // SIGNAL NAME
    // //////////////////////

    /**
     * @return Option<string>
     */
    public function getSignalName(): Option
    {
        return $this->signalNameOption;
    }
    // ///////////////////////
    // EXTERAL EVENT CLASS
    // //////////////////////

    /**
     * @return Option<class-string<TEXTERNAL_EVENT>>
     */
    public function getExteralEventClass(): Option
    {
        return $this->externalEventClassOption;
    }

    /**
     * @template _TEXTERNAL_EVENT
     *
     * @param class-string<_TEXTERNAL_EVENT> $externalEventClass
     *
     * @return RootSignalConfig<TVALUE,_TEXTERNAL_EVENT>
     */
    public function setExteralEventClass(string $externalEventClass): RootSignalConfig
    {
        $this->externalEventClassOption = Option::some($externalEventClass);

        return $this;
    }

    // ///////////////////////
    // Filter
    // //////////////////////

    /**
     * @template _TVALUE
     *
     * @param callable(_TVALUE):bool $filter
     *
     * @return RootSignalConfig<_TVALUE,TEXTERNAL_EVENT>
     */
    public function setFilter(callable $filter): RootSignalConfig
    {
        $this->filter = Option::some($filter);

        return $this;
    }

    /**
     * @return Option<callable(TVALUE):bool>
     */
    public function getFilter(): Option
    {
        return $this->filter;
    }

    // ///////////////////////
    // Clock
    // //////////////////////

    /**
     * @return RootSignalConfig<TVALUE,TEXTERNAL_EVENT>
     */
    public function setClock(ClockInterface $clock): RootSignalConfig
    {
        $this->clock = $clock;

        return $this;
    }

    public function getClock(): ClockInterface
    {
        return $this->clock;
    }
}
