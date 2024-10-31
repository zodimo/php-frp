<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zodimo\FRP\Models\DerivedSignalConfig;
use Zodimo\FRP\Models\RootSignalConfig;
use Zodimo\FRP\Models\RootSignalConfigBuilder;

class SignalConfigFactoryService
{
    private ?EventDispatcherInterface $eventDispatcher;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->eventDispatcher = null;
        $this->container = $container;
    }

    public static function create(ContainerInterface $container): SignalConfigFactoryService
    {
        return new self($container);
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return RootSignalConfig<mixed,mixed>
     */
    public function createRootSignalConfig(?string $signalName = null): RootSignalConfig
    {
        return RootSignalConfig::create($this->getEventDispatcher(), $signalName);
    }

    /**
     * @template TVALUE
     * @template TEVENTOBJ
     *
     * @param RootSignalConfigBuilder<TVALUE,TEVENTOBJ> $builder
     *
     * @return RootSignalConfig<TVALUE,TEVENTOBJ>
     */
    public function createRootSignalConfigFromBuilder(RootSignalConfigBuilder $builder): RootSignalConfig
    {
        return RootSignalConfig::createFromBuilder($builder, $this->getEventDispatcher());
    }

    /**
     * @return DerivedSignalConfig<mixed>
     */
    public function createDerivedSignalConfig(?string $signalName = null): DerivedSignalConfig
    {
        return DerivedSignalConfig::create($this->getEventDispatcher(), $signalName);
    }

    private function getEventDispatcher(): EventDispatcherInterface
    {
        if (is_null($this->eventDispatcher)) {
            $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
        }

        return $this->eventDispatcher;
    }
}
