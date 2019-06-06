<?php

namespace Oro\TwigInspector\Twig;

use Twig\Extension\AbstractExtension;

/**
 * Registers DebugInfoNodeVisitor to add comments to twig templates
 */
class TwigInspectorExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getNodeVisitors()
    {
        return [new DebugInfoNodeVisitor()];
    }
}
