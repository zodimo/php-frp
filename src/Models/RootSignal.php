<?php

declare(strict_types=1);

namespace Zodimo\FRP\Models;

use Zodimo\BaseReturn\Option;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\RootSignalInterface;

/**
 * @template TVALUE
 * @template TEVENTOBJ
 *
 * @implements RootSignalInterface<TVALUE,TEVENTOBJ>
 */
class RootSignal implements RootSignalInterface
{
    private string $id;

    /**
     * @var TVALUE
     */
    private $value;

    /**
     * @var RootSignalConfig<TVALUE,TEVENTOBJ>
     */
    private RootSignalConfig $config;

    /**
     * @var Option<string>
     */
    private Option $nameOption;

    /**
     * @var Option<\DateTimeImmutable>
     */
    private Option $timestampOption;

    /**
     * @var callable(TVALUE,TVALUE):bool
     */
    private $compare;

    /**
     * @param TVALUE                             $value
     * @param RootSignalConfig<TVALUE,TEVENTOBJ> $config
     */
    private function __construct(
        string $id,
        $value,
        RootSignalConfig $config
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->config = $config;

        $this->timestampOption = Option::none();
        $this->compare = $config->getCompareFunction();
        $this->nameOption = $config->getSignalName();
    }

    /**
     * @template _TVALUE
     * @template _TEVENTOBJ
     *
     * @param _TVALUE                              $value
     * @param RootSignalConfig<_TVALUE,_TEVENTOBJ> $config
     *
     * @return RootSignal<_TVALUE,_TEVENTOBJ>
     */
    public static function create(
        string $id,
        $value,
        RootSignalConfig $config
    ): RootSignal {
        return new self($id, $value, $config);
    }

    public function receive(ExternalSignalValueEvent $event): void
    {
        $that = $this;
        $this->config->getFilter()->match(
            function ($filter) use ($event, $that) {
                if ($filter($event->getValue())) {
                    $that->setValue($event->getValue(), $event->getTimeStep());
                }
            },
            function () use ($event, $that) {
                $that->setValue($event->getValue(), $event->getTimeStep());
            }
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Option<\DateTimeImmutable>
     */
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
     * @param TVALUE $value
     */
    public function setValue($value, ?\DateTimeImmutable $timestamp = null): void
    {
        $changed = !$this->equals($value);
        $timestamp ??= $this->config->getClock()->now();
        $internalEvent = InternalSignalValueEvent::create($this->id, $timestamp, $changed);
        if ($changed) {
            $this->timestampOption = Option::some($timestamp);
            $this->value = $value;
        }
        $this->config->getEventDispatcher()->dispatch($internalEvent);
    }

    /**
     * @param TVALUE $other
     */
    private function equals($other): bool
    {
        $compare = $this->compare;

        return $compare($this->value, $other);
    }
}
