<?php

declare(strict_types=1);

namespace App\Tests\PhpUnit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class KernelSmokeTest extends KernelTestCase
{
    public function testKernelBoots(): void
    {
        self::bootKernel(['environment' => 'test']);

        $this->assertSame('test', self::$kernel->getEnvironment());
    }
}
