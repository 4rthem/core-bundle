<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NameValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Name) {
            throw new UnexpectedTypeException($constraint, Name::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match('#^[\w '.preg_quote($constraint->allowedSpecialChars, '#').']+$#usi', $value)) {
            $this->context->addViolation($constraint->message, ['{{ value }}' => $value]);
        }
    }
}
