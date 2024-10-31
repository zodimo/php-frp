<?php

declare(strict_types=1);

namespace Zodimo\FRP\Listeners;

interface ListenerInterface
{
    /**
     *  Returns an array of event names.
     *
     * @return string[]
     */
    public function listen(): array;

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event): void;
}
