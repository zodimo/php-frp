<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;
use Zodimo\FRP\EffectSignalInterface;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\SignalInterface;

/**
 * @template TIN
 * @template TSUCCESS
 * @template TFAILURE
 *
 * @implements EffectSignalInterface<TSUCCESS,TFAILURE>
 */
class EffectSignal implements EffectSignalInterface
{
    private string $id;

    /**
     * @var callable(mixed):IOMonad<TSUCCESS,TFAILURE>
     */
    private $func;

    /**
     * @var SignalInterface<TIN>
     */
    private SignalInterface $parentSignal;

    /**
     * @var EffectSignalConfig<TSUCCESS,TFAILURE>
     */
    private EffectSignalConfig $config;

    /**
     * @var Option<string>
     */
    private Option $nameOption;

    /**
     * @var Option<\DateTimeImmutable>
     */
    private Option $timestampOption;

    /**
     * @var Option<IOMonad<TSUCCESS,TFAILURE>>
     */
    private Option $result;

    /**
     * @param callable(TIN):IOMonad<TSUCCESS,TFAILURE> $func
     * @param SignalInterface<TIN>                     $parentSignal
     * @param EffectSignalConfig<TSUCCESS,TFAILURE>    $config
     */
    private function __construct(
        string $id,
        $func,
        SignalInterface $parentSignal,
        EffectSignalConfig $config
    ) {
        $this->id = $id;
        $this->func = $func;
        $this->parentSignal = $parentSignal;
        $this->config = $config;

        $this->timestampOption = Option::none();
        $this->nameOption = Option::none();
        $this->result = Option::none();
        $this->runInitialEffect();
    }

    /**
     * @template _TSUCCESS
     * @template _TFAILURE
     * @template _TIN
     *
     * @param callable(_TIN):IOMonad<_TSUCCESS,_TFAILURE> $func
     * @param SignalInterface<_TIN>                       $parentSignal
     * @param EffectSignalConfig<_TSUCCESS,_TFAILURE>     $config
     *
     * @return EffectSignal<_TIN,_TSUCCESS,_TFAILURE>
     */
    public static function create(
        string $id,
        callable $func,
        SignalInterface $parentSignal,
        EffectSignalConfig $config
    ): EffectSignal {
        return new self($id, $func, $parentSignal, $config);
    }

    public function hasParentSignal(string $signalId): bool
    {
        return $this->parentSignal->getId() == $signalId;
    }

    public function receive(InternalSignalValueEvent $event): void
    {
        $sourceSignalId = $event->getSourceSignalId();
        if ($this->hasParentSignal($sourceSignalId) and $event->hasChanged()) {
            $this->runEffectWithFilter(Option::some($event->getTimeStep()));
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
     * @return Option<IOMonad<TSUCCESS,TFAILURE>>
     */
    public function getValue(): Option
    {
        return $this->result;
    }

    public function runInitialEffect(): void
    {
        if ($this->config->getRunOnInitialValue()) {
            $this->runEffectWithFilter($this->parentSignal->getTimestamp());
        }
    }

    /**
     * @return TIN
     */
    private function getUpdatedValue()
    {
        /**
         * get a fresh set of signal values.
         */
        return $this->parentSignal->getValue();
    }

    /**
     * @param Option<\DateTimeImmutable> $timestampOption
     */
    private function runEffectWithFilter(Option $timestampOption): void
    {
        $that = $this;
        $this->config->getFilter()->match(
            function (callable $filter) use ($that, $timestampOption): void {
                $newValue = $that->getUpdatedValue();
                if ($filter($newValue)) {
                    $that->runEffect($newValue, $timestampOption);
                }
            },
            function () use ($that, $timestampOption): void {
                $newValue = $that->getUpdatedValue();
                $that->runEffect($newValue, $timestampOption);
            }
        );
    }

    /**
     * @param TIN                        $value
     * @param Option<\DateTimeImmutable> $timestampOption
     */
    private function runEffect($value, Option $timestampOption): void
    {
        $func = $this->func;
        $this->result = Option::some(
            $func($value)->tapSuccess(
                fn ($success) => $this->config->getOnSuccess()->match(
                    fn ($handler) => $handler($success),
                    fn () => IOMonad::pure(null),// noop
                ),
            )->tapFailure(
                fn ($failure) => $this->config->getOnFailure()->match(
                    fn ($handler) => $handler($failure),
                    fn () => IOMonad::pure(null),// noop
                )
            )
        );

        $this->timestampOption = $timestampOption;
    }
}
