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
    public string $minMessage = 'age.min';

    public string $maxMessage = 'age.max';

    public int $minAge = 18;

    public int $maxAge = 130;

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
