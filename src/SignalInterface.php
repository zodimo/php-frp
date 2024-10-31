<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\BaseReturn\Option;

/**
 * @template TVALUE
 */
interface SignalInterface
{
    /**
     * @return TVALUE
     */
    public function getValue();

    public function getId(): string;

    /**
     * @return Option<\DateTimeImmutable>
     */
    public function getTimestamp(): Option;

    public function getNameOrId(): string;
}
