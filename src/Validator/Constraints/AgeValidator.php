<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AgeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Age) {
            throw new UnexpectedTypeException($constraint, Age::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new UnexpectedValueException($value, \DateTimeInterface::class);
        }

        $age = (int) $value->diff(new \DateTimeImmutable())->format('%y');
        if ($age < $constraint->minAge) {
            $this->context->addViolation($constraint->minMessage, ['{{ min_age }}' => $constraint->minAge]);
        }
        if ($age > $constraint->maxAge) {
            $this->context->addViolation($constraint->maxMessage, ['{{ max_age }}' => $constraint->maxAge]);
        }
    }
}
