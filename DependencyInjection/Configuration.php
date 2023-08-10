<?php

namespace Arthem\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('arthem_core');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('mailer')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('sender_address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->defaultValue('%arthem.project_title%')->end()
                        ->scalarNode('mode')->defaultValue('default')->end()
                        ->arrayNode('translation_mailer')
                            ->canBeEnabled()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
