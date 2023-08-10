<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\HeaderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\TemplateWrapper;

class Mailer implements MailerInterface, LoggerAwareInterface
{
    protected SymfonyMailerInterface $mailer;
    protected Environment $twig;
    private TokenStorageInterface $tokenStorage;

    /**
     * @var string|array
     */
    private $fromEmail;

    /**
     * @var MessageProcessorInterface[]
     */
    private array $processors = [];

    private LoggerInterface $logger;
    private RenderingContext $renderingContext;

    public function __construct(
        SymfonyMailerInterface $mailer,
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        RenderingContext $renderingContext,
        $fromEmail,
        LoggerInterface $logger = null
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;

        if (is_array($fromEmail)) {
            $address = array_keys($fromEmail)[0];
            $this->fromEmail = new Address(
                $address,
                $fromEmail[$address]
            );
        } else {
            $this->fromEmail = $fromEmail;
        }
        $this->setLogger($logger ?? new NullLogger());
        $this->renderingContext = $renderingContext;
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
        array|string $toEmail,
        array $params = [],
        array|string $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email {
        if (null === $fromEmail) {
            $fromEmail = $this->fromEmail;
        }

        return $this->sendMessage($templateName, $params, $fromEmail, $toEmail, $attachments, $headers, $options);
    }

    public function sendToUser(
        string $templateName,
        array $params = [],
        MailerUserInterface $user = null,
        array|string $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email {
        if (null === $user) {
            $user = $this->getUser();
            if (!$user instanceof MailerUserInterface) {
                throw new AccessDeniedHttpException('User is not defined for mail');
            }
        }

        if ($user instanceof MailerLocaleUserInterface) {
            $options['locale'] = $user->getLocale();
        }

        return $this->send($templateName, $user->getEmail(), array_merge([
            'user' => $user,
        ], $params), $fromEmail, $attachments, $headers, $options);
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
     * {@inheritdoc}
     *
     * @noinspection PhpInternalEntityUsedInspection
     */
    protected function sendMessage(
        string $templateName,
        array $context,
        $fromEmail,
        $toEmail,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email {
        if (isset($options['locale'])) {
            $this->renderingContext->setLocale($options['locale']);
        }
        $context['recipient_email'] = $toEmail;

        $context = $this->twig->mergeGlobals($context);
        /** @var TemplateWrapper $template */
        /** @noinspection PhpInternalEntityUsedInspection */
        $template = $this->twig->load($templateName);
        /** @noinspection PhpInternalEntityUsedInspection */
        $subject = $template->renderBlock('subject', $context);
        /** @noinspection PhpInternalEntityUsedInspection */
        $textBody = $template->renderBlock('body_text', $context);
        /** @noinspection PhpInternalEntityUsedInspection */
        $htmlBody = $template->renderBlock('body_html', $context);

        /** @var Email $message */
        $message = (new Email())
            ->subject($subject)
            ->from($fromEmail)
            ->to($toEmail);

        if (isset($options['reply_to'])) {
            $message->replyTo($options['reply_to']);
        }

        $messageHeaders = $message->getHeaders();
        foreach ($headers as $key => $value) {
            if ($value instanceof HeaderInterface) {
                $messageHeaders->add($value);
            } elseif (is_array($value)) {
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
            $message->html($htmlBody);
        }
        $message->text($textBody);

        foreach ($attachments as $src) {
            $message->attachFromPath($src);
        }

        foreach ($this->processors as $processor) {
            $processor->process($message);
        }

        $this->mailer->send($message);

        $this->logger->info('Email sent', [
            'email' => $toEmail,
            'template' => $templateName,
        ]);

        return $message;
    }
}
