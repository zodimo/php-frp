<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\SignalFactoryService;
use Zodimo\FRP\Tests\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class RuntimeTest extends TestCase
{
    use MockClosureTrait;
    use FrpTestingEnvironmentFactoryTrait;

    public SignalFactoryService $signalFactoryService;

    public SignalConfigFactoryService $signalConfigFactoryService;

    public Runtime $runtime;

    public function setUp(): void
    {
        $frpEnv = $this->createFrpTestEnvironment();
        $this->runtime = $frpEnv->runtime;
        $this->signalConfigFactoryService = SignalConfigFactoryService::create($frpEnv->container);
        $this->signalFactoryService = new SignalFactoryService($frpEnv->runtime);
    }

    public function testCanGetSigalByName(): void
    {
        $signalName = 'some-name';
        $signalConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $signal = $this->signalFactoryService->createRootSignal(10, $signalConfig);
        $signal->setName($signalName);

        $actualSignalOption = $this->runtime->getSignalByName($signalName);
        $actualSignal = $actualSignalOption->unwrap($this->createClosureNotCalled());
        $this->assertSame($signal, $actualSignal);
    }
}
