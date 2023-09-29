<?php

namespace Oro\TwigInspector\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class OroTwigInspectorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $container->findDefinition('oro_twig_inspector.twig.html_comments_extension')
            ->replaceArgument(3, $config['skip_blocks']);
    }
}
