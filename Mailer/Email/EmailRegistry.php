<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

class EmailRegistry
{
    /**
     * @var array
     */
    private $definitions = [];

    public function addEmailDefinition(string $type, string $definition)
    {
        if (isset($this->definitions[$type])) {
            throw new \InvalidArgumentException(sprintf('Definition "%s" already exists', $type));
        }
        $this->definitions[$type] = $definition;
    }

    public function getEmailKeys(): array
    {
        return array_keys($this->definitions);
    }

    public function getEmail(string $key): EmailDefinitionInterface
    {
        return new $this->definitions[$key];
    }
}
