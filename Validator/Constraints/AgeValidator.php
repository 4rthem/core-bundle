<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgeValidator extends ConstraintValidator
{
    /**
     * @param mixed          $value
     * @param Age|Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var \DateTime $value */
        if (null !== $value) {
            $age = (int) $value->diff(new \DateTime())->format('%y');
            if ($age < (int) $constraint->minAge) {
                $this->context->addViolation($constraint->minMessage, ['{{ min_age }}' => $constraint->minAge]);
            }
            if ($age > (int) $constraint->maxAge) {
                $this->context->addViolation($constraint->maxMessage, ['{{ max_age }}' => $constraint->maxAge]);
            }
        }
    }
}
