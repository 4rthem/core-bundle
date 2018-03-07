<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class Age extends Constraint
{
    public $minMessage = 'age.min';

    public $maxMessage = 'age.max';

    public $minAge = 18;

    public $maxAge = 130;

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
