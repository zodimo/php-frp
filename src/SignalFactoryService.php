<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\FRP\Models\DerivedSignal;
use Zodimo\FRP\Models\DerivedSignalConfig;
use Zodimo\FRP\Models\RootSignal;
use Zodimo\FRP\Models\RootSignalConfig;

class SignalFactoryService
{
    private Runtime $runtime;

    public function __construct(
        Runtime $runtime
    ) {
        $this->runtime = $runtime;
    }

    public static function create(Runtime $runtime): SignalFactoryService
    {
        return new self($runtime);
    }

    /**
     * @template TVALUE
     * @template TEVENTOBJ
     *
     * @param TVALUE                             $initialValue
     * @param RootSignalConfig<TVALUE,TEVENTOBJ> $config
     *
     * @return RootSignal<TVALUE,TEVENTOBJ>
     */
    public function createRootSignal($initialValue, RootSignalConfig $config): RootSignal
    {
        $signal = RootSignal::create($this->runtime->createGuid(), $initialValue, $config);

        $config->getExteralEventClass()->match(
            fn (string $externalEventClass) => $this->runtime->registerRootSignal($signal, $externalEventClass),
            fn () => $this->runtime->registerRootSignal($signal)
        );

        return $signal;
    }

    /**
     * CONST Signal cannot receive external.
     *
     * @template TVALUE
     *
     * @param TVALUE                         $initialValue
     * @param RootSignalConfig<TVALUE,mixed> $config
     *
     * @return RootSignal<TVALUE,mixed>
     */
    public function createConstSignal($initialValue, RootSignalConfig $config): SignalInterface
    {
        $signal = RootSignal::create($this->runtime->createGuid(), $initialValue, $config);
        $this->runtime->registerRootSignal($signal);

        return $signal;
    }

    /**
     * @template _TVALUE
     *
     * @param callable(mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed,?mixed):_TVALUE $func
     * @param array<SignalInterface<mixed>>                                            $parentSignals
     * @param DerivedSignalConfig<_TVALUE>                                             $config
     *
     * @return DerivedSignalInterface<_TVALUE>
     */
    public function createDerivedSignal(callable $func, array $parentSignals, DerivedSignalConfig $config): DerivedSignalInterface
    {
        $signal = DerivedSignal::create(
            $this->runtime->createGuid(),
            $func,
            $parentSignals,
            $config
        );

        $this->runtime->registerDerivedSignal($signal);

        return $signal;
    }
}
