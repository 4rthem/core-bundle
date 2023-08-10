<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class DateRange extends Constraint
{
    public string $message = 'date_range.invalid';

    public string $startDate = 'startDate';

    public string $endDate = 'endDate';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
