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
class RootSignalConfigBuilder
{
    /**
     * @var Option<callable(TVALUE,TVALUE):bool>
     */
    private Option $compareOption;

    /**
     * @var Option<EventDispatcherInterface>
     */
    private Option $eventDispatcherOption;

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
    private Option $filterOption;

    /**
     * @var Option<ClockInterface>
     */
    private Option $clockOption;

    /**
     * @param Option<callable(TVALUE,TVALUE):bool>  $compareOption
     * @param Option<EventDispatcherInterface>      $eventDispatcherOption
     * @param Option<string>                        $signalNameOption
     * @param Option<class-string<TEXTERNAL_EVENT>> $externalEventClassOption
     * @param Option<callable(TVALUE):bool>         $filterOption
     * @param Option<ClockInterface>                $clockOption
     */
    private function __construct(
        Option $compareOption,
        Option $eventDispatcherOption,
        Option $signalNameOption,
        Option $externalEventClassOption,
        Option $filterOption,
        Option $clockOption
    ) {
        $this->compareOption = $compareOption;
        $this->eventDispatcherOption = $eventDispatcherOption;
        $this->signalNameOption = $signalNameOption;
        $this->externalEventClassOption = $externalEventClassOption;
        $this->filterOption = $filterOption;
        $this->clockOption = $clockOption;
    }

    /**
     * @return RootSignalConfigBuilder<mixed,mixed>
     */
    public static function create(): RootSignalConfigBuilder
    {
        return new self(
            Option::none(),
            Option::none(),
            Option::none(),
            Option::none(),
            Option::none(),
            Option::none(),
        );
    }

    // ///////////////////////
    // compare
    // //////////////////////
    /**
     * @return Option<callable(TVALUE,TVALUE):bool>
     */
    public function getCompareFunction(): Option
    {
        return $this->compareOption;
    }

    /**
     * @template _TVALUE
     *
     * @param callable(_TVALUE,_TVALUE):bool $func
     *
     * @return RootSignalConfigBuilder<_TVALUE,TEXTERNAL_EVENT>
     */
    public function setCompareFunction(callable $func): RootSignalConfigBuilder
    {
        $this->compareOption = Option::some($func);

        return $this;
    }

    // ///////////////////////
    // EVENT DISPATCHER
    // //////////////////////

    /**
     * @return Option<EventDispatcherInterface>
     */
    public function getEventDispatcher(): Option
    {
        return $this->eventDispatcherOption;
    }

    /**
     * @return RootSignalConfigBuilder<TVALUE,TEXTERNAL_EVENT>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): RootSignalConfigBuilder
    {
        $this->eventDispatcherOption = Option::some($eventDispatcher);

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

    /**
     * @return RootSignalConfigBuilder<TVALUE,TEXTERNAL_EVENT>
     */
    public function setSignalName(string $name): RootSignalConfigBuilder
    {
        $this->signalNameOption = Option::some($name);

        return $this;
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
     * @return RootSignalConfigBuilder<TVALUE,_TEXTERNAL_EVENT>
     */
    public function setExteralEventClass(string $externalEventClass): RootSignalConfigBuilder
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
     * @return RootSignalConfigBuilder<_TVALUE,TEXTERNAL_EVENT>
     */
    public function setFilter(callable $filter): RootSignalConfigBuilder
    {
        $this->filterOption = Option::some($filter);

        return $this;
    }

    /**
     * @return Option<callable(TVALUE):bool>
     */
    public function getFilter(): Option
    {
        return $this->filterOption;
    }

    // ///////////////////////
    // Clock
    // //////////////////////

    /**
     * @return RootSignalConfigBuilder<TVALUE,TEXTERNAL_EVENT>
     */
    public function setClock(ClockInterface $clock): RootSignalConfigBuilder
    {
        $this->clockOption = Option::some($clock);

        return $this;
    }

    /**
     * @return Option<ClockInterface>
     */
    public function getClock(): Option
    {
        return $this->clockOption;
    }
}
