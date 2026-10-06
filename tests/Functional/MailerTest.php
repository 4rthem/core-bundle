<?php

namespace Arthem\Bundle\CoreBundle\Tests\Functional;

use Arthem\Bundle\CoreBundle\Command\SendEmailCommand;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailRegistry;
use Arthem\Bundle\CoreBundle\Mailer\Mailer;
use Arthem\Bundle\CoreBundle\Mailer\MailerInterface;
use Arthem\Bundle\CoreBundle\Tests\Fixtures\WelcomeEmailDefinition;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Mime\Email;

final class MailerTest extends KernelTestCase
{
    public function testMailerServicesAreWired(): void
    {
        $container = self::getContainer();

        self::assertInstanceOf(Mailer::class, $container->get(MailerInterface::class));

        $registry = $container->get('test.email_registry');
        self::assertInstanceOf(EmailRegistry::class, $registry);
        self::assertSame(['welcome'], $registry->getDefinitions());
        self::assertInstanceOf(WelcomeEmailDefinition::class, $registry->getDefinition('welcome'));
    }

    public function testSendRendersTheTemplateBlocks(): void
    {
        $mailer = self::getContainer()->get(MailerInterface::class);

        $email = $mailer->send('welcome.html.twig', 'alice@wonderland.test', ['name' => 'Alice'], headers: ['X-Rabbit' => 'white']);

        self::assertEmailCount(1);
        self::assertSame('Welcome Alice', $email->getSubject());
        self::assertEmailAddressContains($email, 'From', 'noreply@wonderland.test');
        self::assertSame('Cheshire Cat', $email->getFrom()[0]->getName());
        self::assertEmailAddressContains($email, 'To', 'alice@wonderland.test');
        self::assertEmailHeaderSame($email, 'X-Message-ID', 'welcome');
        self::assertEmailHeaderSame($email, 'X-Rabbit', 'white');
        self::assertEmailHeaderSame($email, 'X-Tea-Party', 'six o\'clock');
        self::assertEmailTextBodyContains($email, 'Hello Alice, this mail goes to alice@wonderland.test.');
        self::assertEmailHtmlBodyContains($email, '<strong>Alice</strong>');
    }

    public function testSendAcceptsNamedRecipients(): void
    {
        $mailer = self::getContainer()->get(MailerInterface::class);

        $email = $mailer->send('@ArthemCore/Mail/test.html.twig', ['hatter@wonderland.test' => 'Mad Hatter'], [], 'queen@wonderland.test');

        self::assertSame('Mad Hatter', $email->getTo()[0]->getName());
        self::assertEmailAddressContains($email, 'From', 'queen@wonderland.test');
    }

    public function testSendEmailCommandSendsTheBundledTestTemplate(): void
    {
        $application = new Application(self::bootKernel());
        $tester = new CommandTester($application->find(SendEmailCommand::COMMAND_NAME));

        $tester->execute(['email' => 'dormouse@wonderland.test', 'template' => '@ArthemCore/Mail/test.html.twig']);

        $tester->assertCommandIsSuccessful();
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertInstanceOf(Email::class, $email);
        self::assertSame('Yo!', trim($email->getSubject()));
        self::assertEmailAddressContains($email, 'To', 'dormouse@wonderland.test');
        self::assertEmailHeaderSame($email, 'X-Message-ID', '@ArthemCore/Mail/test');
    }
}
