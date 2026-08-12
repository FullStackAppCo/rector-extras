<?php

declare(strict_types=1);

namespace FullStackAppCo\RectorExtras\Tests\Feature\Rules;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Rector\Testing\Fixture\FixtureFileFinder;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class HelperFunctionToFacadeRectorTest extends AbstractRectorTestCase
{
    public static function provider(): Iterator
    {
        return FixtureFileFinder::yieldDirectory(__DIR__.'/../Fixtures');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }

    #[Test]
    #[DataProvider('provider')]
    public function it_converts_helpers_to_facades(string $filePath): void
    {
        $this->doTestFile($filePath);
    }
}
