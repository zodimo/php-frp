<?php

declare(strict_types=1);

namespace Zodimo\FRP\Events;

/**
 * This event Triggers a PULL for the value to the signal.
 */
class InternalSignalValueEvent
{
    private string $sourceSignalId;
    private \DateTimeImmutable $timeStep;
    private bool $changed;

    private function __construct(string $sourceSignalId, \DateTimeImmutable $timeStep, bool $changed)
    {
        $this->sourceSignalId = $sourceSignalId;
        $this->timeStep = $timeStep;
        $this->changed = $changed;
    }

    public static function create(string $sourceSignalId, \DateTimeImmutable $timeStep, bool $changed): InternalSignalValueEvent
    {
        return new self($sourceSignalId, $timeStep, $changed);
    }

    public function getSourceSignalId(): string
    {
        return $this->sourceSignalId;
    }

    public function getTimeStep(): \DateTimeImmutable
    {
        return $this->timeStep;
    }

    public function hasChanged(): bool
    {
        return $this->changed;
    }
}
