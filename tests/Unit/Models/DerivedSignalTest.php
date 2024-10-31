<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturn\Option;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Models\DerivedSignal;
use Zodimo\FRP\Models\DerivedSignalConfig;
use Zodimo\FRP\Models\RootSignal;

/**
 * @internal
 *
 * @coversNothing
 */
class DerivedSignalTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $id = 'some-id';

        $rootSignal = $this->createMock(RootSignal::class);
        $rootSignal->expects($this->once())->method('getValue')->willReturn(10);
        $func = $this->createClosureMock();
        $func->expects($this->once())->method('__invoke')->willReturn(10);

        $derivedSignalConfig = $this->createMock(DerivedSignalConfig::class);

        /**
         * @var callable(int ...$inputs):int $func
         */
        $signal = DerivedSignal::create($id, $func, [$rootSignal], $derivedSignalConfig);
        $this->assertInstanceOf(DerivedSignal::class, $signal);
        $this->assertEquals(10, $signal->getValue());
        $this->assertInstanceOf(Option::class, $signal->getTimestamp());
    }
}
