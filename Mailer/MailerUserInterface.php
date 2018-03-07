<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MailerUserInterface
{
    /**
     * @return string
     */
    public function getEmail();
}
