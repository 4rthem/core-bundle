<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MailerLocaleUserInterface
{
    public function getLocale(): ?string;
}
