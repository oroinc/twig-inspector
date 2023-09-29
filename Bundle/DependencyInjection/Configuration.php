<?php

namespace Oro\TwigInspector\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_NODE_NAME = 'oro_twig_inspector';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE_NAME);
        // define an array of skipped blocks
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('skip_blocks')
                    ->info('List of blocks to skip')
                    ->scalarPrototype()->end()
                ->end();

        return $treeBuilder;
    }
}
