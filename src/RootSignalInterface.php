<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\FRP\Events\ExternalSignalValueEvent;

/**
 * @template TVALUE
 * @template TEVENTOBJ
 *
 * @extends SignalInterface<TVALUE>
 */
interface RootSignalInterface extends SignalInterface
{
    /**
     * @param ExternalSignalValueEvent<TVALUE,TEVENTOBJ> $event
     */
    public function receive(ExternalSignalValueEvent $event): void;

    /**
     * @param TVALUE $value
     */
    public function setValue($value, ?\DateTimeImmutable $timestamp = null): void;
}
