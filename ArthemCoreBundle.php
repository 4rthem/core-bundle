<?php

namespace Arthem\Bundle\CoreBundle;

use Arthem\Bundle\CoreBundle\DependencyInjection\Compiler\EmailDefinitionPass;
use Arthem\Bundle\CoreBundle\DependencyInjection\Compiler\MessageProcessorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArthemCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MessageProcessorPass());
        $container->addCompilerPass(new EmailDefinitionPass());
    }
}
