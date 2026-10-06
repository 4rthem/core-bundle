<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DateRange extends Constraint
{
    public string $message = 'date_range.invalid';

    public string $startDate = 'startDate';

    public string $endDate = 'endDate';

    /**
     * @param string[]|null $groups
     */
    public function __construct(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->startDate = $startDate ?? $this->startDate;
        $this->endDate = $endDate ?? $this->endDate;
        $this->message = $message ?? $this->message;
    }

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
