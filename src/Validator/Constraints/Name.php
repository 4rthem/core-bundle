<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Name extends Constraint
{
    public string $message = 'name.invalid';

    public string $allowedSpecialChars = '-\'’';

    /**
     * @param string[]|null $groups
     */
    public function __construct(
        ?string $allowedSpecialChars = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->allowedSpecialChars = $allowedSpecialChars ?? $this->allowedSpecialChars;
        $this->message = $message ?? $this->message;
    }
}
