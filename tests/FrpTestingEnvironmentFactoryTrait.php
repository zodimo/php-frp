<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests;

use Zodimo\FRP\Listeners\ListenerInterface;

trait FrpTestingEnvironmentFactoryTrait
{
    /**
     * @param array<ListenerInterface> $listeners
     */
    public function createFrpTestEnvironment(array $listeners = []): FrpTestingEnvironment
    {
        return FrpTestingEnvironment::create($listeners);
    }
}
