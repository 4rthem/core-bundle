<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\HeaderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class Mailer implements MailerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private Address $fromEmail;

    /**
     * @var MessageProcessorInterface[]
     */
    private array $processors = [];

    public function __construct(
        private readonly SymfonyMailerInterface $mailer,
        private readonly Environment $twig,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RenderingContext $renderingContext,
        string|array $fromEmail,
        ?LoggerInterface $logger = null,
    ) {
        if (is_array($fromEmail)) {
            $address = array_keys($fromEmail)[0];
            $this->fromEmail = new Address($address, $fromEmail[$address]);
        } else {
            $this->fromEmail = new Address($fromEmail);
        }
        $this->setLogger($logger ?? new NullLogger());
    }

    public function addProcessor(MessageProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }

    public function send(
        string $templateName,
        array|string $toEmail,
        array $params = [],
        array|string|null $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = [],
    ): Email {
        if (null === $fromEmail) {
            $fromEmail = $this->fromEmail;
        }

        return $this->sendMessage($templateName, $params, $fromEmail, $toEmail, $attachments, $headers, $options);
    }

    public function sendToUser(
        string $templateName,
        array $params = [],
        ?MailerUserInterface $user = null,
        array|string|null $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = [],
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

        $toEmail = $user->getEmail();
        if (empty($toEmail)) {
            throw new \InvalidArgumentException(\sprintf('Trying to send mail "%s" to an empty email address', $templateName));
        }

        return $this->send($templateName, $toEmail, array_merge([
            'user' => $user,
        ], $params), $fromEmail, $attachments, $headers, $options);
    }

    protected function getUser(): ?MailerUserInterface
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

    protected function sendMessage(
        string $templateName,
        array $context,
        Address|array|string $fromEmail,
        array|string $toEmail,
        array $attachments = [],
        array $headers = [],
        array $options = [],
    ): Email {
        if (isset($options['locale'])) {
            $this->renderingContext->setLocale($options['locale']);
        }
        $context['recipient_email'] = $toEmail;

        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = (new Email())
            ->subject($subject)
            ->from(...$this->toAddresses($fromEmail))
            ->to(...$this->toAddresses($toEmail));

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

    /**
     * @param Address|string|array<int|string, Address|string> $addresses a single address, a list of addresses or an email => name map
     *
     * @return Address[]
     */
    private function toAddresses(Address|array|string $addresses): array
    {
        if (!is_array($addresses)) {
            return [$addresses instanceof Address ? $addresses : new Address($addresses)];
        }

        $result = [];
        foreach ($addresses as $key => $value) {
            $result[] = match (true) {
                $value instanceof Address => $value,
                is_string($key) => new Address($key, $value),
                default => new Address($value),
            };
        }

        return $result;
    }
}
