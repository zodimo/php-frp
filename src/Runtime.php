<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Ramsey\Uuid\Uuid;
use Zodimo\BaseReturn\Option;
use Zodimo\FRP\Events\ExternalSignalValueEvent;
use Zodimo\FRP\Events\InternalSignalValueEvent;

class Runtime
{
    /**
     * @var array<string>
     */
    private array $uuids;

    /**
     * @var array<string,RootSignalInterface<mixed,mixed>>
     */
    private array $rootSignals;

    /**
     * @var array<string,DerivedSignalInterface<mixed>>
     */
    private array $derivedSignals;

    /**
     * @var array<string,class-string>
     */
    private array $externalSignalEvents;

    public function __construct(
    ) {
        $this->uuids = [];
        $this->rootSignals = [];
        $this->derivedSignals = [];
        $this->externalSignalEvents = [];
    }

    public function createGuid(): string
    {
        $uuid = Uuid::uuid4()->toString();
        while (true) {
            $uuid = Uuid::uuid4()->toString();
            if (!in_array($uuid, $this->uuids, true)) {
                $this->uuids[] = $uuid;

                break;
            }
        }

        return $uuid;
    }

    /**
     * @template TVALUE
     * @template TEVENTOBJ
     *
     * @param RootSignalInterface<TVALUE,TEVENTOBJ> $signal
     * @param ?class-string<TEVENTOBJ>              $externalEventClass
     */
    public function registerRootSignal(RootSignalInterface $signal, ?string $externalEventClass = null): void
    {
        $this->removeSignal($signal);
        $this->rootSignals[$signal->getId()] = $signal;
        if (!is_null($externalEventClass)) {
            $this->addExternalSignalEventForSignal($externalEventClass, $signal->getNameOrId());
        }
    }

    /**
     * @param DerivedSignalInterface<mixed> $signal
     */
    public function registerDerivedSignal(DerivedSignalInterface $signal): void
    {
        $this->derivedSignals[$signal->getId()] = $signal;
    }

    /**
     * @param SignalInterface<mixed> $signal
     */
    public function removeSignal(SignalInterface $signal): void
    {
        $signalId = $signal->getId();

        switch (true) {
            case $signal instanceof RootSignalInterface:
                if (key_exists($signalId, $this->rootSignals)) {
                    $signal = $this->rootSignals[$signalId];
                    $this->removeExternalEventForSignal($signal->getNameOrId());
                    unset($this->rootSignals[$signalId]);
                }

                break;

            case $signal instanceof DerivedSignalInterface:
                if (key_exists($signalId, $this->derivedSignals)) {
                    unset($this->derivedSignals[$signalId]);
                }

                break;
        }
    }

    /**
     * PUSH external value to root signal.
     *
     * @param ExternalSignalValueEvent<mixed,mixed> $event
     */
    public function notifyRootSignals(ExternalSignalValueEvent $event): void
    {
        $eventClass = $event->getExternalEventClassName();
        if (in_array($eventClass, $this->externalSignalEvents, true)) {
            foreach ($this->rootSignals as $rootSignal) {
                if ($event->getTargetSignalNameOrId() == $rootSignal->getNameOrId()) {
                    $rootSignal->receive($event);
                }
            }
        }
    }

    /**
     * Trigger the PULL action on derived signls.
     */
    public function notifyDerivedSignals(InternalSignalValueEvent $event): void
    {
        foreach ($this->derivedSignals as $derivedSignal) {
            if ($derivedSignal->hasParentSignal($event->getSourceSignalId())) {
                $derivedSignal->receive($event);
            }
        }
    }

    /**
     * @return Option<SignalInterface<mixed>>
     */
    public function getSignalByName(string $name): Option
    {
        $signals = array_merge($this->rootSignals, $this->derivedSignals);
        foreach ($signals as $signal) {
            if ($signal->getNameOrId() === $name) {
                return Option::some($signal);
            }
        }

        return Option::none();
    }

    /**
     * @param class-string $externalEventClass
     */
    private function addExternalSignalEventForSignal(string $externalEventClass, string $signalNameOrId): void
    {
        if (!key_exists($signalNameOrId, $this->externalSignalEvents)) {
            $this->externalSignalEvents[$signalNameOrId] = $externalEventClass;
        }
    }

    private function removeExternalEventForSignal(string $signalNameOrId): void
    {
        if (!key_exists($signalNameOrId, $this->externalSignalEvents)) {
            unset($this->externalSignalEvents[$signalNameOrId]);
        }
    }
}
