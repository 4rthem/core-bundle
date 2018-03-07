<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Mailer implements MailerInterface, LoggerAwareInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string|array
     */
    private $fromEmail;

    /**
     * @var MessageProcessorInterface[]
     */
    private $processors = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        $fromEmail,
        LoggerInterface $logger = null
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->fromEmail = $fromEmail;
        $this->setLogger($logger ?? new NullLogger());
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addProcessor(MessageProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    public function send(
        string $templateName,
        $toEmail,
        array $params = [],
        $fromEmail = null,
        array $attachments = [],
        array $headers = []
    ): \Swift_Message {
        if (null === $fromEmail) {
            $fromEmail = $this->fromEmail;
        }

        return $this->sendMessage($templateName, $params, $fromEmail, $toEmail, $attachments, $headers);
    }

    public function sendToUser(
        string $templateName,
        array $params = [],
        MailerUserInterface $user = null,
        $fromEmail = null,
        array $attachments = [],
        array $headers = []
    ): \Swift_Message {
        if (null === $user) {
            $user = $this->getUser();
            if (!$user instanceof UserInterface) {
                throw new AccessDeniedHttpException('User is not defined for mail');
            }
        }

        return $this->send($templateName, $user->getEmail(), array_merge([
            'user' => $user,
        ], $params), $fromEmail, $attachments, $headers);
    }

    /**
     * @return MailerUserInterface|null
     */
    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if ($user instanceof MailerUserInterface) {
            return $user;
        }

        if ($user instanceof MailerSecurityUserInterface) {
            return $user->getMailerUser();
        }

        return null;
    }

    /**
     * @param string       $templateName
     * @param array        $context
     * @param array|string $fromEmail
     * @param array|string $toEmail
     * @param array        $attachments  An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     * @param array        $headers
     *
     * @return \Swift_Message
     *
     * @noinspection PhpInternalEntityUsedInspection
     */
    protected function sendMessage(
        string $templateName,
        array $context,
        $fromEmail,
        $toEmail,
        array $attachments = [],
        array $headers = []
    ): \Swift_Message {
        $context['recipient_email'] = $toEmail;

        $context = $this->twig->mergeGlobals($context);
        /** @var \Twig_Template $template */
        /** @noinspection PhpInternalEntityUsedInspection */
        $template = $this->twig->loadTemplate($templateName);
        /** @noinspection PhpInternalEntityUsedInspection */
        $subject = $template->renderBlock('subject', $context);
        /** @noinspection PhpInternalEntityUsedInspection */
        $textBody = $template->renderBlock('body_text', $context);
        /** @noinspection PhpInternalEntityUsedInspection */
        $htmlBody = $template->renderBlock('body_html', $context);

        /** @var \Swift_Message $message */
        $message = (new \Swift_Message($subject))
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        $messageHeaders = $message->getHeaders();
        foreach ($headers as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $messageHeaders->addTextHeader($key, $v);
                }
            } else {
                $messageHeaders->addTextHeader($key, $value);
            }
        }

        if (!$messageHeaders->has('X-Message-ID')) {
            $messageHeaders->addTextHeader(
                'X-Message-ID',
                preg_replace('#(\.html)?\.twig$#', '', $templateName)
            );
        }

        if ($htmlBody) {
            $message->setBody($htmlBody, 'text/html');
            $message->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        foreach ($attachments as $src) {
            if (!$src instanceof \Swift_Mime_Attachment) {
                $src = \Swift_Attachment::fromPath($src);
            }
            $message->attach($src);
        }

        foreach ($this->processors as $processor) {
            $processor->process($message);
        }

        $this->mailer->send($message);

        $this->logger->info(sprintf('Email sent', [
            'email' => $toEmail,
            'template' => $templateName,
        ]));

        return $message;
    }
}
