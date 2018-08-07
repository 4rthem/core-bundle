<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

class EmailRegistry
{
    /**
     * @var EmailDefinitionInterface[]
     */
    private $definitions = [];

    public function addEmailDefinition(EmailDefinitionInterface $definition)
    {
        $key = $definition::getType();
        if (isset($this->definitions[$key])) {
            throw new \InvalidArgumentException(sprintf('Definition "%s" already exists', $key));
        }
        $this->definitions[$key] = $definition;
    }

    public function getEmailKeys(): array
    {
        return array_keys($this->definitions);
    }

    public function getEmail(string $key): EmailDefinitionInterface
    {
        return $this->definitions[$key];
    }
}
