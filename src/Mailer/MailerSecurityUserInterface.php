<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MailerSecurityUserInterface
{
    public function getMailerUser(): MailerUserInterface;
}
