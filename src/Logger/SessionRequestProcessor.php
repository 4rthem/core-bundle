<?php

namespace Arthem\Bundle\CoreBundle\Logger;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function processRecord(LogRecord $record): LogRecord
    {
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException) {
            return $record;
        }
        if (!$session->isStarted()) {
            return $record;
        }

        $sessionId = substr($session->getId(), 0, 8) ?: '????????';

        $record->extra['token'] = $sessionId.'-'.substr(uniqid('', true), -8);

        return $record;
    }
}
