<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{
    /**
     * @param mixed                $entity
     * @param DateRange|Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $start = $propertyAccessor->getValue($entity, $constraint->startDate);
        $end = $propertyAccessor->getValue($entity, $constraint->endDate);

        if (null !== $start && null !== $end && $start > $end) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->endDate)
                ->addViolation();
        }
    }
}
