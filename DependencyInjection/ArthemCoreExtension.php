<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection;

use Arthem\Bundle\CoreBundle\Form\GoogleAutoCompleteType;
use Arthem\Bundle\CoreBundle\Mailer\Mailer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ArthemCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('logger.yml');

        if ($config['mailer']['enabled']) {
            $loader->load('mailer.yml');

            $sender = [$config['mailer']['sender_address'] => $config['mailer']['sender_name']];
            $definition = $container->getDefinition(Mailer::class);
            $definition->setArgument('$fromEmail', $sender);

            if ($config['mailer']['translation_mailer']['enabled']) {
                $loader->load('translation_mailer.yml');
            }
        }

        if (isset($config['form']['google_auto_complete']['enabled'])) {
            $loader->load('form/google_auto_complete.yml');
            $definition = $container->getDefinition(GoogleAutoCompleteType::class);
            $definition->replaceArgument(0, $config['form']['google_auto_complete']['api_key']);
        }
    }
}
