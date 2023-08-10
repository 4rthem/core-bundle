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
    public ?array $map = null;

    /**
     * @var string|null
     */
    public ?string $testCallback = null;

    public function __construct(mixed $options = null)
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
    public function getDefaultOption(): ?string
    {
        return 'map';
    }
}
