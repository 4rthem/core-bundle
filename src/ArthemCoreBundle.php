<?php

namespace Arthem\Bundle\CoreBundle;

use Arthem\Bundle\CoreBundle\DependencyInjection\Compiler\EmailDefinitionPass;
use Arthem\Bundle\CoreBundle\DependencyInjection\Compiler\MessageProcessorPass;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailDefinitionInterface;
use Arthem\Bundle\CoreBundle\Mailer\Mailer;
use Arthem\Bundle\CoreBundle\Mailer\MessageProcessorInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ArthemCoreBundle extends AbstractBundle
{
    public const EMAIL_DEFINITION_TAG = 'arthem_core.email_definition';
    public const MESSAGE_PROCESSOR_TAG = 'arthem_core.message_processor';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MessageProcessorPass());
        $container->addCompilerPass(new EmailDefinitionPass());
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $rootNode = $definition->rootNode();
        \assert($rootNode instanceof ArrayNodeDefinition);

        $mailer = $rootNode->children()->arrayNode('mailer')->canBeEnabled();
        $mailer->children()->scalarNode('sender_address')->isRequired()->cannotBeEmpty();
        $mailer->children()->scalarNode('sender_name')->defaultValue('%arthem.project_title%');
        $mailer->children()->scalarNode('mode')->defaultValue('default');
        $mailer->children()->arrayNode('translation_mailer')->canBeEnabled();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/logger.yaml');

        if (!$config['mailer']['enabled']) {
            return;
        }

        $container->import('../config/mailer.yaml');
        $container->import('../config/commands.yaml');

        $builder->getDefinition(Mailer::class)
            ->setArgument('$fromEmail', [$config['mailer']['sender_address'] => $config['mailer']['sender_name']]);

        $builder->registerForAutoconfiguration(MessageProcessorInterface::class)
            ->addTag(self::MESSAGE_PROCESSOR_TAG);

        if ($config['mailer']['translation_mailer']['enabled']) {
            $container->import('../config/translation_mailer.yaml');

            $builder->registerForAutoconfiguration(EmailDefinitionInterface::class)
                ->addTag(self::EMAIL_DEFINITION_TAG);
        }
    }
}
