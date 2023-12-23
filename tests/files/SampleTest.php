<?php

declare(strict_types=1);

namespace Uss\Test;

use PHPUnit\Framework\TestCase;
final class SampleTest extends TestCase
{
    public function testSample(): void
    {
        $this->assertEquals(1 + 1, 2, 'Excellent');
    }
}
