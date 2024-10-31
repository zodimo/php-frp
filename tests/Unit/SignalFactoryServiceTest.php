<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalFactoryService;

/**
 * @internal
 *
 * @coversNothing
 */
class SignalFactoryServiceTest extends TestCase
{
    public function testCanCreate(): void
    {
        $runtimeMock = $this->createMock(Runtime::class);

        $service = new SignalFactoryService($runtimeMock);

        $this->assertInstanceOf(SignalFactoryService::class, $service);
    }
}
