<?php

namespace Arthem\Bundle\CoreBundle\Tests\Fixtures;

use Arthem\Bundle\CoreBundle\Mailer\Email\EmailDefinitionInterface;

final class WelcomeEmailDefinition implements EmailDefinitionInterface
{
    public static function getType(): string
    {
        return 'welcome';
    }
}
