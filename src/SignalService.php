<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\BaseReturn\Option;
use Zodimo\BaseReturn\Tuple;
use Zodimo\FRP\Models\DerivedSignalConfig;
use Zodimo\FRP\Models\RootSignalConfig;
use Zodimo\FRP\Models\RootSignalConfigBuilder;

class SignalService
{
    private SignalFactoryService $signalFactoryService;
    private SignalConfigFactoryService $signalConfigFactoryService;
    private Runtime $runtime;

    public function __construct(
        SignalFactoryService $signalFactoryService,
        SignalConfigFactoryService $signalConfigFactoryService,
        Runtime $runtime
    ) {
        $this->signalFactoryService = $signalFactoryService;
        $this->signalConfigFactoryService = $signalConfigFactoryService;
        $this->runtime = $runtime;
    }

    /**
     * @return Option<SignalInterface<mixed>>
     */
    public function getSignalByName(string $name): Option
    {
        return $this->runtime->getSignalByName($name);
    }

    /**
     * Const is a RootSignal without listening to external events.
     *
     * @template TVALUE
     * @template TEVENTOBJ
     *
     * @param TVALUE                              $value
     * @param ?RootSignalConfig<TVALUE,TEVENTOBJ> $signalConfig
     *
     * @return RootSignalInterface<TVALUE,TEVENTOBJ>
     */
    public function createRootSignal($value, ?RootSignalConfig $signalConfig = null): RootSignalInterface
    {
        $signalConfig ??= $this->signalConfigFactoryService->createRootSignalConfig();

        return $this->signalFactoryService->createRootSignal($value, $signalConfig);
    }

    /**
     * Const is a RootSignal without listening to external events.
     *
     * @template TVALUE
     * @template TEVENTOBJ
     *
     * @param TVALUE                                                                                        $value
     * @param callable(RootSignalConfigBuilder<TVALUE,TEVENTOBJ>):RootSignalConfigBuilder<TVALUE,TEVENTOBJ> $builder
     *
     * @return RootSignalInterface<TVALUE,TEVENTOBJ>
     */
    public function createRootSignalWithConfigBuilder($value, callable $builder): RootSignalInterface
    {
        $configBuilder = RootSignalConfigBuilder::create();
        $populatedConfigBuilder = $builder($configBuilder);
        $signalConfig = $this->signalConfigFactoryService->createRootSignalConfigFromBuilder($populatedConfigBuilder);

        return $this->signalFactoryService->createRootSignal($value, $signalConfig);
    }

    /**
     * Const is a RootSignal without listening to external events.
     *
     * @template TVALUE
     *
     * @param TVALUE $value
     *
     * @return RootSignalInterface<TVALUE,mixed>
     */
    public function const($value): RootSignalInterface
    {
        $config = $this->signalConfigFactoryService->createRootSignalConfig();

        return $this->signalFactoryService->createRootSignal($value, $config);
    }

    /**
     * Generic names, TOValue: output signal value, TIValue1: input signal 1 value, etc...,.
     *
     * @template TOVALUE
     * @template TIVALUE1
     *
     * @param callable(TIVALUE1):TOVALUE    $func
     * @param SignalInterface<TIVALUE1>     $signal1
     * @param ?DerivedSignalConfig<TOVALUE> $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function lift(callable $func, SignalInterface $signal1, ?DerivedSignalConfig $signalConfig = null): DerivedSignalInterface
    {
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->signalFactoryService->createDerivedSignal($func, [$signal1], $signalConfig);
    }

    /**
     * Generic names, TOValue: output signal value, TIValue1: input signal 1 value, etc...
     *
     * @template TOVALUE
     * @template TIVALUE1
     * @template TIVALUE2
     *
     * @param callable(TIVALUE1,TIVALUE2):TOVALUE $func
     * @param SignalInterface<TIVALUE1>           $signal1
     * @param SignalInterface<TIVALUE2>           $signal2
     * @param ?DerivedSignalConfig<TOVALUE>       $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function lift2(callable $func, SignalInterface $signal1, SignalInterface $signal2, ?DerivedSignalConfig $signalConfig = null): DerivedSignalInterface
    {
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->signalFactoryService->createDerivedSignal($func, [$signal1, $signal2], $signalConfig);
    }

    /**
     * Generic names, TOValue: output signal value, TIValue1: input signal 1 value, etc...
     *
     * @template TOVALUE
     * @template TIVALUE1
     * @template TIVALUE2
     * @template TIVALUE3
     *
     * @param callable(TIVALUE1,TIVALUE2,TIVALUE3):TOVALUE $func
     * @param SignalInterface<TIVALUE1>                    $signal1
     * @param SignalInterface<TIVALUE2>                    $signal2
     * @param SignalInterface<TIVALUE3>                    $signal3
     * @param ?DerivedSignalConfig<TOVALUE>                $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function lift3(
        callable $func,
        SignalInterface $signal1,
        SignalInterface $signal2,
        SignalInterface $signal3,
        ?DerivedSignalConfig $signalConfig = null
    ): DerivedSignalInterface {
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->signalFactoryService->createDerivedSignal($func, [$signal1, $signal2, $signal3], $signalConfig);
    }

    /**
     * Generic names, TOValue: output signal value, TIValue1: input signal 1 value, etc...
     *
     * @template TOVALUE
     * @template TIVALUE1
     * @template TIVALUE2
     * @template TIVALUE3
     * @template TIVALUE4
     *
     * @param callable(TIVALUE1,TIVALUE2,TIVALUE3,TIVALUE4):TOVALUE $func
     * @param SignalInterface<TIVALUE1>                             $signal1
     * @param SignalInterface<TIVALUE2>                             $signal2
     * @param SignalInterface<TIVALUE3>                             $signal3
     * @param SignalInterface<TIVALUE4>                             $signal4
     * @param ?DerivedSignalConfig<TOVALUE>                         $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function lift4(
        callable $func,
        SignalInterface $signal1,
        SignalInterface $signal2,
        SignalInterface $signal3,
        SignalInterface $signal4,
        ?DerivedSignalConfig $signalConfig = null
    ): DerivedSignalInterface {
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->signalFactoryService->createDerivedSignal($func, [$signal1, $signal2, $signal3, $signal4], $signalConfig);
    }

    /**
     * Generic names, TOValue: output signal value, TIValue1: input signal 1 value, etc...
     *
     * @template TOVALUE
     * @template TIVALUE1
     * @template TIVALUE2
     * @template TIVALUE3
     * @template TIVALUE4
     * @template TIVALUE5
     *
     * @param callable(TIVALUE1,TIVALUE2,TIVALUE3,TIVALUE4):TOVALUE $func
     * @param SignalInterface<TIVALUE1>                             $signal1
     * @param SignalInterface<TIVALUE2>                             $signal2
     * @param SignalInterface<TIVALUE3>                             $signal3
     * @param SignalInterface<TIVALUE4>                             $signal4
     * @param SignalInterface<TIVALUE5>                             $signal5
     * @param ?DerivedSignalConfig<TOVALUE>                         $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function lift5(
        callable $func,
        SignalInterface $signal1,
        SignalInterface $signal2,
        SignalInterface $signal3,
        SignalInterface $signal4,
        SignalInterface $signal5,
        ?DerivedSignalConfig $signalConfig = null
    ): DerivedSignalInterface {
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->signalFactoryService->createDerivedSignal($func, [$signal1, $signal2, $signal3, $signal4, $signal5], $signalConfig);
    }

    /**
     * @template TIVALUE
     * @template TOVALUE
     * @template TACC of TOVALUE
     *
     * @param callable(TACC,TIVALUE):TOVALUE $func
     * @param TACC                           $initialValue
     * @param SignalInterface<TIVALUE>       $signal
     * @param ?DerivedSignalConfig<TOVALUE>  $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function foldp(
        callable $func,
        $initialValue,
        SignalInterface $signal,
        ?DerivedSignalConfig $signalConfig = null
    ): DerivedSignalInterface {
        $lastValue = $initialValue;
        $innerFunc = function ($input) use (&$lastValue, $func) {
            // elm foldp :: (a → b → b) → b → Signal a → Signal b
            // >>> foldl :: Foldable t => (b -> a -> b) -> b -> t a -> b

            $lastValue = $func($lastValue, $input);

            return $lastValue;
        };
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->lift($innerFunc, $signal, $signalConfig);
    }

    /**
     * Detect Transition between 2 specific subsequent values with $from and $to predicated. Will return $func($value) on true else $defaultValue.
     *
     * @template TIVALUE
     * @template TOVALUE
     *
     * @param callable(TIVALUE):bool        $from
     * @param callable(TIVALUE):bool        $to
     * @param callable(TIVALUE):TOVALUE     $func
     * @param TOVALUE                       $defaultValue
     * @param SignalInterface<TIVALUE>      $signal
     * @param ?DerivedSignalConfig<TOVALUE> $signalConfig
     *
     * @return DerivedSignalInterface<TOVALUE>
     */
    public function transition(callable $from, callable $to, callable $func, $defaultValue, SignalInterface $signal, ?DerivedSignalConfig $signalConfig = null): DerivedSignalInterface
    {
        /**
         * Higher order signal.
         * Stateful function.
         */
        /**
         * @var callable(TIVALUE,TIVALUE):Tuple<bool,TIVALUE>
         */
        $foldpTransitionFunc = function (Tuple $acc, $item) use ($from, $to): Tuple {
            $transitionOccurred = $from($acc->snd()) and $to($item);

            return Tuple::create($transitionOccurred, $item);
        };

        $transitionSignal = $this->foldp($foldpTransitionFunc, Tuple::create($defaultValue, $signal->getValue()), $signal);

        $func = function (Tuple $transitionResult) use ($func, $defaultValue) {
            if ($transitionResult->fst()) {
                return $func($transitionResult->snd());
            }

            return $defaultValue;
        };
        $signalConfig ??= $this->signalConfigFactoryService->createDerivedSignalConfig();

        return $this->lift($func, $transitionSignal, $signalConfig);
    }
}
