<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturn\Option;

/**
 * @template TVALUE
 */
class DerivedSignalConfig
{
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var Option<string>
     */
    private Option $signalNameOption;

    /**
     * @var Option<callable(TVALUE):bool>
     */
    private Option $filter;

    /**
     * @param Option<string>                $signalNameOption
     * @param Option<callable(TVALUE):bool> $filter
     */
    private function __construct(
        EventDispatcherInterface $eventDispatcher,
        Option $signalNameOption,
        Option $filter
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->signalNameOption = $signalNameOption;
        $this->filter = $filter;
    }

    /**
     * @return DerivedSignalConfig<mixed>
     */
    public static function create(EventDispatcherInterface $eventDispatcher, ?string $signalName = null): DerivedSignalConfig
    {
        if (is_null($signalName)) {
            $signalNameOption = Option::none();
        } else {
            $signalNameOption = Option::some($signalName);
        }

        return new self(
            $eventDispatcher,
            $signalNameOption,
            Option::none(),
        );
    }

    // ///////////////////////
    // EVENT DISPATCHER
    // //////////////////////
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @return DerivedSignalConfig<TVALUE>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): DerivedSignalConfig
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
    // Filter
    // //////////////////////

    /**
     * @template _TVALUE
     *
     * @param callable(_TVALUE):bool $filter
     *
     * @return DerivedSignalConfig<_TVALUE>
     */
    public function setFilter(callable $filter): DerivedSignalConfig
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
}
