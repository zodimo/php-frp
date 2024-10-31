<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\FRP\Events\InternalSignalValueEvent;

/**
 * @template TVALUE
 *
 * @extends SignalInterface<TVALUE>
 */
interface DerivedSignalInterface extends SignalInterface
{
    public function receive(InternalSignalValueEvent $event): void;

    public function hasParentSignal(string $signalId): bool;
}
