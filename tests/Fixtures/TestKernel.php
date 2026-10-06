<?php

namespace Arthem\Bundle\CoreBundle\Tests\Fixtures;

use Arthem\Bundle\CoreBundle\ArthemCoreBundle;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailRegistry;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new TwigBundle();
        yield new MonologBundle();
        yield new ArthemCoreBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    private function getConfigDir(): string
    {
        return $this->getProjectDir().'/var/config';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test',
            'test' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => ['log' => true],
            'mailer' => ['dsn' => 'null://null'],
            'translator' => ['default_path' => '%kernel.project_dir%/tests/Fixtures/translations', 'fallbacks' => ['en']],
            'validation' => ['email_validation_mode' => 'html5'],
        ]);

        $container->extension('security', [
            'providers' => ['memory' => ['memory' => null]],
            'firewalls' => ['main' => ['lazy' => true, 'provider' => 'memory']],
        ]);

        $container->extension('twig', [
            'default_path' => '%kernel.project_dir%/tests/Fixtures/templates',
            'strict_variables' => true,
        ]);

        $container->extension('monolog', [
            'handlers' => ['test' => ['type' => 'test']],
        ]);

        $container->extension('arthem_core', [
            'mailer' => [
                'sender_address' => 'noreply@wonderland.test',
                'sender_name' => 'Cheshire Cat',
                'translation_mailer' => true,
            ],
        ]);

        $services = $container->services()->defaults()->autowire()->autoconfigure();
        $services->set(WelcomeEmailDefinition::class);
        $services->set(TeaPartyProcessor::class);
        $services->alias('test.email_registry', EmailRegistry::class)->public();
    }
}
