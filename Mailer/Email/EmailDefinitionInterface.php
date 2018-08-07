<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

interface EmailDefinitionInterface
{
    public static function getType(): string;
}
