<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NameValidator extends ConstraintValidator
{
    /**
     * @param mixed           $value
     * @param Name|Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null !== $value && '' !== $value) {
            if (!preg_match('#^[\w '.preg_quote($constraint->allowedSpecialChars, '#').']+$#usi', $value)) {
                $this->context->addViolation($constraint->message, ['{{ value }}' => $value]);
            }
        }
    }
}
