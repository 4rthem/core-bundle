<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection\Compiler;

use Arthem\Bundle\CoreBundle\Mailer\Email\EmailDefinitionInterface;
use Arthem\Bundle\CoreBundle\Mailer\Email\EmailRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EmailDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(EmailRegistry::class)) {
            return;
        }

        $registry = $container->getDefinition(EmailRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('arthem_core.email_definition');

        $ids = [];
        foreach ($taggedServices as $id => $tags) {
            /** @var EmailDefinitionInterface $class */
            $class = $id;
            $ids[$class::getType()] = $id;
        }

        ksort($ids);

        foreach ($ids as $id) {
            $registry->addMethodCall('addEmailDefinition', [new Reference($id)]);
        }
    }
}
