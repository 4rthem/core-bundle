<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Validate) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Validate');
        }

        if (null === $value) {
            return;
        }

        /* @var $context ExecutionContextInterface */
        $context = $this->context;
        $validationGroup = $context->getGroup();

        $groups = [$validationGroup];
        if (isset($constraint->map[$validationGroup])) {
            $groups = $constraint->map[$validationGroup];
        }

        if (null !== $constraint->testCallback) {
            if (false === $value->{$constraint->testCallback}()) {
                return;
            }
        }

        $context->getValidator()
            ->inContext($context)
            ->validate($value, null, $groups);
    }
}
