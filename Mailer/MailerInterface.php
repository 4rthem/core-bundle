<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Symfony\Component\Mime\Email;

interface MailerInterface
{
    /**
     * @param array $attachments An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     */
    public function send(
        string $templateName,
        array|string $toEmail,
        array $params = [],
        array|string $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email;

    /**
     * @param array               $attachments An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     */
    public function sendToUser(
        string $templateName,
        array $params = [],
        MailerUserInterface $user = null,
        array|string $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email;
}
