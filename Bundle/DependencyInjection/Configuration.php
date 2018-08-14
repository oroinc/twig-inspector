<?php

namespace Oro\TwigInspector\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Define `oro_twig_inspector.base_dir` configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oro_twig_inspector');

        $rootNode
            ->children()
                ->scalarNode('base_dir')
                    ->info(
                        'Base directory where to open files from. For security reasons files outside of the base_dir'.
                        'cannot be open. By default this parameter match kernel.project_dir.'
                    )
                    ->defaultValue('%kernel.project_dir%')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
