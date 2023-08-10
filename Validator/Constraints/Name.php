<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class Name extends Constraint
{
    public string $message = 'name.invalid';

    public string $allowedSpecialChars = '-\'’';
}
