<?php

namespace Arthem\Bundle\CoreBundle\Tests\Unit\Validator;

use Arthem\Bundle\CoreBundle\Validator\Constraints\Validate;
use PHPUnit\Framework\TestCase;

final class ValidateTest extends TestCase
{
    public function testGroupsDefaultToTheMappedGroups(): void
    {
        $constraint = new Validate(map: ['Default' => ['payment'], 'checkout' => ['payment', 'shipping']]);

        self::assertSame(['Default', 'checkout'], $constraint->groups);
    }

    public function testExplicitGroupsAreMergedWithTheMappedOnes(): void
    {
        $constraint = new Validate(map: ['checkout' => ['payment']], groups: ['Default', 'checkout']);

        self::assertSame(['Default', 'checkout'], $constraint->groups);
    }

    public function testWithoutMapTheConstraintBelongsToTheDefaultGroup(): void
    {
        self::assertSame(['Default'], (new Validate(testCallback: 'isReady'))->groups);
    }
}
