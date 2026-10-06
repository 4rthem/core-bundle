<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Cascades validation to the annotated object, mapping the current group to the nested groups.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Validate extends Constraint
{
    /**
     * @var array<string, string[]>|null current group => groups applied to the nested object
     */
    public ?array $map = null;

    public ?string $testCallback = null;

    /**
     * @param array<string, string[]>|null $map
     * @param string[]|null                $groups
     */
    public function __construct(
        ?array $map = null,
        ?string $testCallback = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if ($map) {
            $groups = array_values(array_unique(array_merge($groups ?? [], array_keys($map))));
        }

        parent::__construct(null, $groups, $payload);

        $this->map = $map;
        $this->testCallback = $testCallback;
    }
}
