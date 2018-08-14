<?php

namespace Oro\TwigInspector\Twig;

use Twig_Extension;

/**
 * Register DebugInfoNodeVisitor to add comments to twig templates
 */
class TwigInspectorExtension extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getNodeVisitors()
    {
        return [new DebugInfoNodeVisitor()];
    }
}
