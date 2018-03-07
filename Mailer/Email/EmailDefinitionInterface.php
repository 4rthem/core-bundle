<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

interface EmailDefinitionInterface
{
    public static function getKey(): string;

    public function getSampleParameters(): array;

    public function getTranslationKeys(): array;
}
