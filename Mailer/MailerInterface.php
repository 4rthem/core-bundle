<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

use Symfony\Component\Mime\Email;

interface MailerInterface
{
    /**
     * @param string|array $toEmail
     * @param string|array $fromEmail
     * @param array        $attachments An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     */
    public function send(
        string $templateName,
        $toEmail,
        array $params = [],
        $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email;

    /**
     * @param MailerUserInterface $user
     * @param array|string        $fromEmail
     * @param array               $attachments An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     */
    public function sendToUser(
        string $templateName,
        array $params = [],
        MailerUserInterface $user = null,
        $fromEmail = null,
        array $attachments = [],
        array $headers = [],
        array $options = []
    ): Email;
}
