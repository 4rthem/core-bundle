<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection\Compiler;

use Arthem\Bundle\CoreBundle\ArthemCoreBundle;
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
        foreach ($container->findTaggedServiceIds(ArthemCoreBundle::MESSAGE_PROCESSOR_TAG) as $id => $tags) {
            $mailer->addMethodCall('addProcessor', [new Reference($id)]);
        }
    }
}
