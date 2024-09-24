<?php

namespace Oro\TwigInspector\Twig;

use Oro\TwigInspector\Twig\Node\NodeEnd;
use Oro\TwigInspector\Twig\Node\NodeStart;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\BodyNode;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

/**
 * Inspired by {@see \Twig\Profiler\NodeVisitor\ProfilerNodeVisitor}
 * Modify generated twig template to add comments before and after every block and template
 */
class DebugInfoNodeVisitor extends AbstractNodeVisitor
{
    protected const EXTENSION_NAME = HtmlCommentsExtension::class;

    #[\Override]
    protected function doEnterNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    #[\Override]
    protected function doLeaveNode(Node $node, Environment $env): Node
    {
        $varName = $this->getVarName();
        if ($node instanceof ModuleNode) {
            $node->setNode(
                'display_start',
                new Node(
                    [
                        new NodeStart(
                            self::EXTENSION_NAME,
                            $node->getTemplateName(),
                            $node->getTemplateLine(),
                            $varName
                        ),
                        $node->getNode('display_start'),
                    ]
                )
            );
            $node->setNode(
                'display_end',
                new Node(
                    [
                        new NodeEnd($varName),
                        $node->getNode('display_end'),
                    ]
                )
            );
        } elseif ($node instanceof BlockNode) {
            $node->setNode(
                'body',
                new BodyNode(
                    [
                        new NodeStart(
                            self::EXTENSION_NAME,
                            $node->getAttribute('name'),
                            $node->getTemplateLine(),
                            $varName
                        ),
                        $node->getNode('body'),
                        new NodeEnd($varName),
                    ]
                )
            );
        }

        return $node;
    }

    private function getVarName(): string
    {
        return sprintf('__inspector_%s', hash('sha256', self::EXTENSION_NAME));
    }

    protected function getReference(Node $node): NodeReference
    {
        return new NodeReference($node->getAttribute('name'), $node->getTemplateName(), $node->getTemplateLine());
    }

    #[\Override]
    public function getPriority(): int
    {
        return 0;
    }
}
