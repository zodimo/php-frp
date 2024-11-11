<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;

/**
 * @template TVALUE
 * @template TFAILURE
 */
class EffectSignalConfig
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
     * @var Option<callable(TVALUE):IOMonad<null,mixed>>
     */
    private Option $onSuccess;

    /**
     * @var Option<callable(TFAILURE):IOMonad<null,mixed>>
     */
    private Option $onFailure;

    private bool $runOnInitialValue;

    /**
     * @param Option<string>                                 $signalNameOption
     * @param Option<callable(TVALUE):bool>                  $filter
     * @param Option<callable(TVALUE):IOMonad<null,mixed>>   $onSuccess
     * @param Option<callable(TFAILURE):IOMonad<null,mixed>> $onFailure
     */
    private function __construct(
        EventDispatcherInterface $eventDispatcher,
        Option $signalNameOption,
        Option $filter,
        Option $onSuccess,
        Option $onFailure,
        bool $runOnInitialValue
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->signalNameOption = $signalNameOption;
        $this->filter = $filter;
        $this->onSuccess = $onSuccess;
        $this->onFailure = $onFailure;
        $this->runOnInitialValue = $runOnInitialValue;
    }

    /**
     * @return EffectSignalConfig<mixed,mixed>
     */
    public static function create(EventDispatcherInterface $eventDispatcher, ?string $signalName = null): EffectSignalConfig
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
            Option::none(),
            Option::none(),
            false,
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
     * @return EffectSignalConfig<TVALUE,TFAILURE>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): EffectSignalConfig
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
     * @return EffectSignalConfig<_TVALUE,TFAILURE>
     */
    public function setFilter(callable $filter): EffectSignalConfig
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
    // onSuccess
    // //////////////////////

    /**
     * @template _TVALUE
     * @template _TONSUCCESS_FAILURE
     *
     * @param callable(_TVALUE):IOMonad<null,_TONSUCCESS_FAILURE> $onSuccess
     *
     * @return EffectSignalConfig<_TVALUE,_TONSUCCESS_FAILURE|TFAILURE>
     */
    public function setOnSuccess(callable $onSuccess): EffectSignalConfig
    {
        $this->onSuccess = Option::some($onSuccess);

        return $this;
    }

    /**
     * @return Option<callable(TVALUE):IOMonad<null,mixed>>
     */
    public function getOnSuccess(): Option
    {
        return $this->onSuccess;
    }

    // ///////////////////////
    // onFailure
    // //////////////////////

    /**
     * @template _TFAILURE
     * @template _TONFAILURE_FAILURE
     *
     * @param callable(_TFAILURE):IOMonad<null,_TONFAILURE_FAILURE> $onFailure
     *
     * @return EffectSignalConfig<_TFAILURE,_TONFAILURE_FAILURE|TFAILURE>
     */
    public function setOnFailure(callable $onFailure): EffectSignalConfig
    {
        $this->onFailure = Option::some($onFailure);

        return $this;
    }

    /**
     * @return Option<callable(TFAILURE):IOMonad<null,mixed>>
     */
    public function getOnFailure(): Option
    {
        return $this->onFailure;
    }

    // ///////////////////////
    // runOnIntialValue
    // //////////////////////

    /**
     * @return EffectSignalConfig<TVALUE,TFAILURE>
     */
    public function setRunOnInitialValue(bool $run): EffectSignalConfig
    {
        $this->runOnInitialValue = $run;

        return $this;
    }

    public function getRunOnInitialValue(): bool
    {
        return $this->runOnInitialValue;
    }
}
