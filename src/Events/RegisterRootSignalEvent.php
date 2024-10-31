<?php

declare(strict_types=1);

namespace Zodimo\FRP\Events;

use Zodimo\FRP\RootSignalInterface;

/**
 * @template TVALUE
 * @template TEVENTOBJ
 */
class RegisterRootSignalEvent
{
    /**
     * @var RootSignalInterface<TVALUE,TEVENTOBJ>
     */
    private RootSignalInterface $signal;

    /**
     * @param RootSignalInterface<TVALUE,TEVENTOBJ> $signal
     */
    private function __construct(RootSignalInterface $signal)
    {
        $this->signal = $signal;
    }

    /**
     * @template _TVALUE
     * @template _TEVENTOBJ
     *
     * @param RootSignalInterface<_TVALUE,_TEVENTOBJ> $signal
     *
     * @return RegisterRootSignalEvent<_TVALUE,_TEVENTOBJ>
     */
    public static function create(RootSignalInterface $signal): RegisterRootSignalEvent
    {
        return new self($signal);
    }

    /**
     * @return RootSignalInterface<TVALUE,TEVENTOBJ>
     */
    public function getSignal(): RootSignalInterface
    {
        return $this->signal;
    }
}
