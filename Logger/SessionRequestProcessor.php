<?php

namespace Arthem\Bundle\CoreBundle\Logger;

use RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionRequestProcessor
{
    private SessionInterface $session;
    private ?string $token = null;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function processRecord(array $record)
    {
        if (null === $this->token) {
            if (!$this->session->isStarted()) {
                return $record;
            }

            try {
                $sessionId = substr($this->session->getId(), 0, 8);
                $this->token = $sessionId;
            } catch (RuntimeException $e) {
                $this->token = 'session-error';
            }
            $this->token .= '-'.substr(uniqid(), -8);
        }

        $record['extra']['token'] = $this->token;

        return $record;
    }
}
