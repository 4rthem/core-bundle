<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{
    /**
     * @param DateRange|Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $start = $propertyAccessor->getValue($value, $constraint->startDate);
        $end = $propertyAccessor->getValue($value, $constraint->endDate);

        if (null !== $start && null !== $end && $start > $end) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->endDate)
                ->addViolation();
        }
    }
}
