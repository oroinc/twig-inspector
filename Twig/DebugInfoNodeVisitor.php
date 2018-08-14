<?php

namespace Oro\TwigInspector\Twig;

use Oro\TwigInspector\Twig\Node\NodeEnd;
use Oro\TwigInspector\Twig\Node\NodeStart;
use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node;
use Twig_Node_Block;
use Twig_Node_Body;
use Twig_Node_Module;

/**
 * Inspired by {@see \Twig_Profiler_NodeVisitor_Profiler}
 * Modify generated twig template to add comments before and after every block and template
 */
class DebugInfoNodeVisitor extends Twig_BaseNodeVisitor
{
    protected const EXTENSION_NAME = HtmlCommentsExtension::class;

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        $varName = $this->getVarName();
        if ($node instanceof Twig_Node_Module) {
            $node->setNode(
                'display_start',
                new Twig_Node(
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
                new Twig_Node(
                    [
                        new NodeEnd($varName),
                        $node->getNode('display_end'),
                    ]
                )
            );
        } elseif ($node instanceof Twig_Node_Block) {
            $node->setNode(
                'body',
                new Twig_Node_Body(
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

    /**
     * @return string
     */
    private function getVarName()
    {
        return sprintf('__inspector_%s', hash('sha256', self::EXTENSION_NAME));
    }

    /**
     * @param Twig_Node $node
     * @return NodeReference
     */
    protected function getReference(Twig_Node $node): string
    {
        return new NodeReference($node->getAttribute('name'), $node->getTemplateName(), $node->getTemplateLine());
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
