<?php

declare(strict_types=1);

namespace Zodimo\FRP\Events;

use Zodimo\FRP\DerivedSignalInterface;

/**
 * @template TVALUE
 */
class RegisterDerivedSignalEvent
{
    /**
     * @var DerivedSignalInterface<TVALUE>
     */
    private DerivedSignalInterface $signal;

    /**
     * @param DerivedSignalInterface<TVALUE> $signal
     */
    private function __construct(DerivedSignalInterface $signal)
    {
        $this->signal = $signal;
    }

    /**
     * @template _TVALUE
     *
     * @param DerivedSignalInterface<_TVALUE> $signal
     *
     * @return RegisterDerivedSignalEvent<_TVALUE>
     */
    public static function create(DerivedSignalInterface $signal): RegisterDerivedSignalEvent
    {
        return new self($signal);
    }

    /**
     * @return DerivedSignalInterface<TVALUE>
     */
    public function getSignal(): DerivedSignalInterface
    {
        return $this->signal;
    }
}
