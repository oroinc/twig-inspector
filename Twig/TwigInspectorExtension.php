<?php

namespace Oro\TwigInspector\Twig;

use Twig\Extension\AbstractExtension;

/**
 * Registers DebugInfoNodeVisitor to add comments to twig templates
 */
class TwigInspectorExtension extends AbstractExtension
{
    public function getNodeVisitors(): array
    {
        return [new DebugInfoNodeVisitor()];
    }
}
