<?php

namespace Arthem\Bundle\CoreBundle\Logger;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SessionRequestProcessor
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string|null
     */
    private $token;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function processRecord(array $record)
    {
        if (null === $this->token) {
            $session = $this->container->get('session');
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
