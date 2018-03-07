<?php

namespace Arthem\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class Validate extends Constraint
{
    /**
     * @var string[]|null
     */
    public $map;

    /**
     * @var string|null
     */
    public $testCallback;

    public function __construct($options = null)
    {
        parent::__construct($options);
        if (null !== $this->map) {
            is_array($this->map) || $this->map = [];
            if ($this->map) {
                isset($this->groups) || $this->groups = [];
                $this->groups = array_unique(array_merge($this->groups, array_keys($this->map)));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'map';
    }
}
