<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zodimo\FRP\Models\DerivedSignal;
use Zodimo\FRP\Models\RootSignal;
use Zodimo\FRP\Runtime;
use Zodimo\FRP\SignalConfigFactoryService;
use Zodimo\FRP\SignalFactoryService;
use Zodimo\FRP\Tests\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class SignalFactoryServiceTest extends TestCase
{
    use FrpTestingEnvironmentFactoryTrait;

    public SignalConfigFactoryService $signalConfigFactoryService;

    public Runtime $runtime;

    public function setUp(): void
    {
        $frpEnv = $this->createFrpTestEnvironment();
        $this->runtime = $frpEnv->runtime;
        $this->signalConfigFactoryService = SignalConfigFactoryService::create($frpEnv->container);
    }

    public function testCanCreate(): void
    {
        $runtimeMock = $this->createMock(Runtime::class);

        $service = new SignalFactoryService($runtimeMock);

        $this->assertInstanceOf(SignalFactoryService::class, $service);
    }

    public function testCanCreateRootSignal(): void
    {
        $service = new SignalFactoryService($this->runtime);

        $defaultValue = 10;
        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $signal = $service->createRootSignal($defaultValue, $rootConfig);
        $this->assertInstanceOf(RootSignal::class, $signal);
    }

    public function testCanCreateDerivedSignal(): void
    {
        $defaultValue = 10;

        /**
         * @var callable(int ...$xs):int $func
         */
        $func = function (int ...$xs) {
            $x = $xs[0];

            return $x + 20;
        };

        $service = new SignalFactoryService($this->runtime);

        $rootConfig = $this->signalConfigFactoryService->createRootSignalConfig();
        $derivedConfig = $this->signalConfigFactoryService->createDerivedSignalConfig();

        $defaultValue = 10;
        $rootSignal = $service->createRootSignal($defaultValue, $rootConfig);
        $derivedsignal = $service->createDerivedSignal($func, [$rootSignal], $derivedConfig);
        $this->assertInstanceOf(DerivedSignal::class, $derivedsignal);
    }
}
