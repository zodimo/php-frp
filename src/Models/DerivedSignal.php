<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Zodimo\BaseReturn\Option;
use Zodimo\FRP\DerivedSignalInterface;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\SignalInterface;

/**
 * @template TVALUE
 *
 * @implements DerivedSignalInterface<TVALUE>
 */
class DerivedSignal implements DerivedSignalInterface
{
    private string $id;

    /**
     * @var callable(mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed):TVALUE
     */
    private $func;

    /**
     * @var array<SignalInterface<mixed>>
     */
    private array $parentSignals;

    /**
     * @var DerivedSignalConfig<TVALUE>
     */
    private DerivedSignalConfig $config;

    /**
     * @var Option<string>
     */
    private Option $nameOption;

    /**
     * @var Option<\DateTimeImmutable>
     */
    private Option $timestampOption;

    /**
     * @var TVALUE
     */
    private $value;

    /**
     * @param mixed                                                                   $func
     * @param callable(mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed):TVALUE $func
     * @param array<SignalInterface<mixed>>                                           $parentSignals
     * @param DerivedSignalConfig<TVALUE>                                             $config
     */
    private function __construct(
        string $id,
        $func,
        array $parentSignals,
        DerivedSignalConfig $config
    ) {
        $this->id = $id;
        $this->func = $func;
        $this->parentSignals = $parentSignals;
        $this->config = $config;

        $this->timestampOption = Option::none();
        $this->nameOption = Option::none();
        $this->update();
    }

    /**
     * @template _TVALUE
     *
     * @param callable(mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed):_TVALUE $func
     * @param array<SignalInterface<mixed>>                                            $parentSignals
     * @param DerivedSignalConfig<_TVALUE>                                             $config
     *
     * @return DerivedSignal<_TVALUE>
     */
    public static function create(
        string $id,
        callable $func,
        array $parentSignals,
        DerivedSignalConfig $config
    ): DerivedSignal {
        return new self($id, $func, $parentSignals, $config);
    }

    public function hasParentSignal(string $signalId): bool
    {
        foreach ($this->parentSignals as $parentSignal) {
            if ($parentSignal->getId() == $signalId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Will work like CombineLatest from ReactiveX,.
     *
     * @see https://reactivex.io/documentation/operators/combinelatest.html
     */
    public function receive(InternalSignalValueEvent $event): void
    {
        $sourceSignalId = $event->getSourceSignalId();
        $that = $this;
        if ($this->hasParentSignal($sourceSignalId)) {
            $changed = false;
            $this->config->getFilter()->match(
                function (callable $filter) use ($that, $event, &$changed): void {
                    if ($event->hasChanged()) {
                        $newValue = $that->getUpdatedValue();
                        if ($filter($newValue)) {
                            $changed = true;
                            $that->value = $newValue;
                            $that->timestampOption = Option::some($event->getTimeStep());
                        }
                    }
                },
                function () use ($that, $event, &$changed): void {
                    if ($event->hasChanged()) {
                        $changed = true;
                        $that->value = $that->getUpdatedValue();
                        $that->timestampOption = Option::some($event->getTimeStep());
                    }
                }
            );

            $internalEvent = InternalSignalValueEvent::create($this->id, $event->getTimeStep(), $changed);
            $this->config->getEventDispatcher()->dispatch($internalEvent);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTimestamp(): Option
    {
        return $this->timestampOption;
    }

    /**
     * @return TVALUE
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getNameOrId(): string
    {
        return $this->nameOption->match(
            fn ($name) => $name,
            fn () => $this->id
        );
    }

    public function setName(string $name): void
    {
        $this->nameOption = Option::some($name);
    }

    /**
     * @return TVALUE
     */
    private function getUpdatedValue()
    {
        /**
         * get a fresh set of signal values.
         */
        $args = array_map(function (SignalInterface $signal) {
            return $signal->getValue();
        }, $this->parentSignals);

        $func = $this->func;

        return $func(...$args);
    }

    private function update(): void
    {
        $this->value = $this->getUpdatedValue();
    }
}
