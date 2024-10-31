<?php

declare(strict_types=1);

namespace Zodimo\FRP\Events;

use Zodimo\FRP\SignalInterface;

/**
 * @template TVALUE
 */
class RemoveSignalEvent
{
    /**
     * @var SignalInterface<TVALUE>
     */
    private SignalInterface $signal;

    /**
     * @param SignalInterface<TVALUE> $signal
     */
    private function __construct(SignalInterface $signal)
    {
        $this->signal = $signal;
    }

    /**
     * @template _TVALUE
     *
     * @param SignalInterface<_TVALUE> $signal
     *
     * @return RemoveSignalEvent<_TVALUE>
     */
    public static function create(SignalInterface $signal): RemoveSignalEvent
    {
        return new self($signal);
    }

    /**
     * @return SignalInterface<TVALUE>
     */
    public function getSignal(): SignalInterface
    {
        return $this->signal;
    }
}
