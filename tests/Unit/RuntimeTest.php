<?php

declare(strict_types=1);

namespace Zodimo\FRP\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\FRP\Runtime;

/**
 * @internal
 *
 * @coversNothing
 */
class RuntimeTest extends TestCase
{
    public function testCanCreateRuntime(): void
    {
        $runtime = new Runtime();
        $this->assertInstanceOf(Runtime::class, $runtime);
    }

    public function testCanCreadGuid(): void
    {
        $runtime = new Runtime();
        $uuid = $runtime->createGuid();
        $this->assertNotEmpty($uuid);
    }
}
