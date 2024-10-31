<?php

declare(strict_types=1);

namespace Zodimo\FRP\Listeners;

use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Events\InternalSignalValueEvent;
use Zodimo\FRP\Events\RegisterDerivedSignalEvent;
use Zodimo\FRP\Events\RegisterRootSignalEvent;
use Zodimo\FRP\Events\RemoveSignalEvent;
use Zodimo\FRP\Runtime;

class FrpRuntimeListener implements ListenerInterface
{
    private Runtime $runtime;

    public function __construct(
        Runtime $runtime
    ) {
        $this->runtime = $runtime;
    }

    public function listen(): array
    {
        return [
            ExternalSignalValueEvent::class,
            InternalSignalValueEvent::class,
            RegisterDerivedSignalEvent::class,
            RegisterRootSignalEvent::class,
            RemoveSignalEvent::class,
        ];
    }

    public function process(object $event): void
    {
        switch (true) {
            case $event instanceof ExternalSignalValueEvent:
                $this->runtime->notifyRootSignals($event);

                break;

            case $event instanceof InternalSignalValueEvent:
                $this->runtime->notifyDerivedSignals($event);

                break;

            case $event instanceof RegisterDerivedSignalEvent:
                $this->runtime->registerDerivedSignal($event->getSignal());

                break;

            case $event instanceof RegisterRootSignalEvent:
                $this->runtime->registerRootSignal($event->getSignal());

                break;

            case $event instanceof RemoveSignalEvent:
                $this->runtime->removeSignal($event->getSignal());

                break;
        }
    }
}
