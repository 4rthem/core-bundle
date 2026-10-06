<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection\Compiler;

use Arthem\Bundle\CoreBundle\ArthemCoreBundle;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailDefinitionInterface;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EmailDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(EmailRegistry::class)) {
            return;
        }

        $registry = $container->getDefinition(EmailRegistry::class);

        $ids = [];
        foreach ($container->findTaggedServiceIds(ArthemCoreBundle::EMAIL_DEFINITION_TAG) as $id => $tags) {
            /** @var class-string<EmailDefinitionInterface> $class */
            $class = $container->getDefinition($id)->getClass() ?? $id;
            $ids[$class::getType()] = $class;
        }

        ksort($ids);

        foreach ($ids as $type => $class) {
            $registry->addMethodCall('addEmailDefinition', [$type, $class]);
        }
    }
}
