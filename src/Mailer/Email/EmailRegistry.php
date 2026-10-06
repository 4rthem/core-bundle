<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

class EmailRegistry
{
    /**
     * @var array<string, class-string<EmailDefinitionInterface>>
     */
    private array $definitions = [];

    /**
     * @param class-string<EmailDefinitionInterface> $definition
     */
    public function addEmailDefinition(string $type, string $definition): void
    {
        if (isset($this->definitions[$type])) {
            throw new \InvalidArgumentException(\sprintf('Definition "%s" already exists', $type));
        }
        $this->definitions[$type] = $definition;
    }

    /**
     * @return string[]
     */
    public function getDefinitions(): array
    {
        return array_keys($this->definitions);
    }

    public function getDefinition(string $type): EmailDefinitionInterface
    {
        return new $this->definitions[$type]();
    }
}
