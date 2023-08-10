<?php

namespace Arthem\Bundle\CoreBundle\Logger;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor
{
    private ?string $token = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function processRecord(LogRecord $record): LogRecord
    {
        $session = $this->requestStack->getCurrentRequest()?->getSession();
        if (null === $session) {
            return $record;
        }

        if (null === $this->token) {
            if (!$session->isStarted()) {
                return $record;
            }

            try {
                $sessionId = substr($session->getId(), 0, 8);
                $this->token = $sessionId;
            } catch (\RuntimeException $e) {
                $this->token = 'session-error';
            }
            $this->token .= '-'.substr(uniqid(), -8);
        }

        $record['extra']['token'] = $this->token;

        return $record;
    }
}
