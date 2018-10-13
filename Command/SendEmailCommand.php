<?php

namespace Arthem\Bundle\CoreBundle\Command;

use Arthem\Bundle\CoreBundle\Mailer\Mailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'arthem:mailer:send';

    protected static $defaultName = self::COMMAND_NAME;


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
        $container = $this->getContainer();

        $toEmail = $input->getArgument('email');
        $template = $input->getArgument('template');

        $attachments = [];
        foreach ($input->getOption('attachment') as $attachment) {
            $attachments[] = realpath($attachment);
        }

        $container
            ->get(Mailer::class)
            ->send($template, $toEmail, [], null, $attachments);
    }
}
