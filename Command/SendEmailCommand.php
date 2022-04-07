<?php

namespace Arthem\Bundle\CoreBundle\Command;

use Arthem\Bundle\CoreBundle\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailCommand extends Command
{
    const COMMAND_NAME = 'arthem:mailer:send';

    protected static $defaultName = self::COMMAND_NAME;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED),
                new InputArgument('template', InputArgument::REQUIRED),
                new InputOption(
                    'attachment',
                    'a',
                    InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED
                ),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $toEmail = $input->getArgument('email');
        $template = $input->getArgument('template');

        $attachments = [];
        foreach ($input->getOption('attachment') as $attachment) {
            $attachments[] = realpath($attachment);
        }

        $this->mailer->send($template, $toEmail, [], null, $attachments);

        return 0;
    }
}
