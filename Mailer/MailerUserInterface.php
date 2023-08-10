<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MailerUserInterface
{
    public function getEmail(): string;
}
