<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Models\EffectSignal;
use Zodimo\FRP\Models\EffectSignalConfig;
use Zodimo\FRP\Models\RootSignal;

/**
 * @internal
 *
 * @coversNothing
 */
class EffectSignalTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreateAndNotRunOnInitialValue(): void
    {
        $id = 'some-id';

        $rootSignal = $this->createMock(RootSignal::class);
        $rootSignal->expects($this->never())->method('getValue');
        $func = $this->createClosureMock();
        $func->expects($this->never())->method('__invoke');

        $signalConfig = $this->createMock(EffectSignalConfig::class);

        /**
         * @var callable(int):IOMonad<int,mixed> $func
         * @var EffectSignalConfig<mixed,mixed>  $signalConfig
         */
        $signal = EffectSignal::create($id, $func, $rootSignal, $signalConfig);
        $this->assertInstanceOf(EffectSignal::class, $signal);
        $this->assertTrue($signal->getValue()->isNone());
        $this->assertInstanceOf(Option::class, $signal->getTimestamp());
    }
}
