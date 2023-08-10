<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection\Compiler;

use Arthem\Bundle\CoreBundle\Mailer\Mailer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MessageProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(Mailer::class)) {
            return;
        }

        $mailer = $container->getDefinition(Mailer::class);
        $taggedServices = $container->findTaggedServiceIds('arthem_core.message_processor');
        foreach ($taggedServices as $id => $tags) {
            $mailer->addMethodCall('addProcessor', [new Reference($id)]);
        }
    }
}
