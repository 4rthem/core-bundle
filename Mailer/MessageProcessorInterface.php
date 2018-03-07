<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MessageProcessorInterface
{
    public function process(\Swift_Message $message);
}
