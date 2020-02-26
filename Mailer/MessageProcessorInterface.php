<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Symfony\Component\Mime\Email;

interface MessageProcessorInterface
{
    public function process(Email $message);
}
