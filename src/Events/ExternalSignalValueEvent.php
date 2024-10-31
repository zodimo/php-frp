<?php

declare(strict_types=1);

namespace Zodimo\FRP\Events;

/**
 * This event PUSHES the value to the signal.
 *
 * @template TVALUE
 * @template EVENTOBJ
 */
class ExternalSignalValueEvent
{
    private string $targetSignalId;
    private \DateTimeImmutable $timeStep;

    /**
     * @var class-string<EVENTOBJ>
     */
    private string $externalEventClassName;

    /**
     * @var TVALUE
     */
    private $value;

    /**
     * @param TVALUE                 $value
     * @param class-string<EVENTOBJ> $externalEventClassName
     */
    private function __construct(string $targetSignalId, \DateTimeImmutable $timeStep, $value, string $externalEventClassName)
    {
        $this->targetSignalId = $targetSignalId;
        $this->timeStep = $timeStep;
        $this->value = $value;
        $this->externalEventClassName = $externalEventClassName;
    }

    /**
     * @template _TVALUE
     * @template _EVENTOBJ
     *
     * @param _TVALUE                 $value
     * @param class-string<_EVENTOBJ> $externalEventClassName
     *
     * @return ExternalSignalValueEvent<_TVALUE,_EVENTOBJ>
     */
    public static function create(string $id, \DateTimeImmutable $timeStep, $value, string $externalEventClassName): ExternalSignalValueEvent
    {
        return new self($id, $timeStep, $value, $externalEventClassName);
    }

    public function getTargetSignalNameOrId(): string
    {
        return $this->targetSignalId;
    }

    public function getTimeStep(): \DateTimeImmutable
    {
        return $this->timeStep;
    }

    /**
     * @return TVALUE
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getExternalEventClassName(): string
    {
        return $this->externalEventClassName;
    }
}
