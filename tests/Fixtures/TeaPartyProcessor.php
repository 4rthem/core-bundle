<?php

namespace Arthem\Bundle\CoreBundle\Tests\Fixtures;

use Arthem\Bundle\CoreBundle\Mailer\MessageProcessorInterface;
use Symfony\Component\Mime\Email;

final class TeaPartyProcessor implements MessageProcessorInterface
{
    public function process(Email $message): void
    {
        $message->getHeaders()->addTextHeader('X-Tea-Party', 'six o\'clock');
    }
}
