<?php

namespace Arthem\Bundle\CoreBundle\Mailer;

interface MailerInterface
{
    /**
     * @param string       $templateName
     * @param string|array $toEmail
     * @param array        $params
     * @param string|array $fromEmail
     * @param array        $attachments  An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     * @param array        $headers
     *
     * @return \Swift_Message
     */
    public function send(
        string $templateName,
        $toEmail,
        array $params = [],
        $fromEmail = null,
        array $attachments = [],
        array $headers = []
    ): \Swift_Message;

    /**
     * @param string              $templateName
     * @param array               $params
     * @param MailerUserInterface $user
     * @param array|string        $fromEmail
     * @param array               $attachments  An array of paths eg: ['/var/www/uploads/a.jpg', '/var/www/static/demo.pdf']
     * @param array               $headers
     *
     * @return \Swift_Message
     */
    public function sendToUser(
        string $templateName,
        array $params = [],
        MailerUserInterface $user = null,
        $fromEmail = null,
        array $attachments = [],
        array $headers = []
    ): \Swift_Message;
}
