<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Age extends Constraint
{
    public string $minMessage = 'age.min';

    public string $maxMessage = 'age.max';

    public int $minAge = 18;

    public int $maxAge = 130;

    /**
     * @param string[]|null $groups
     */
    public function __construct(
        ?int $minAge = null,
        ?int $maxAge = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->minAge = $minAge ?? $this->minAge;
        $this->maxAge = $maxAge ?? $this->maxAge;
        $this->minMessage = $minMessage ?? $this->minMessage;
        $this->maxMessage = $maxMessage ?? $this->maxMessage;
    }

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
