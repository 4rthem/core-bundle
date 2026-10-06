<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateRangeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DateRange) {
            throw new UnexpectedTypeException($constraint, DateRange::class);
        }

        if (null === $value) {
            return;
        }

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
