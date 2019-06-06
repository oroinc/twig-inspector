<?php

namespace Oro\TwigInspector\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Modify generated Twig template to call the `start` method of HtmlCommentsExtension extension
 */
class NodeStart extends Node
{
    public function __construct($exensionName, $name, $line, $varName)
    {
        parent::__construct(
            [],
            ['extension_name' => $exensionName, 'name'=> $name, 'line' => $line, 'var_name' => $varName]
        );
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write(sprintf('$%s = $this->env->getExtension(', $this->getAttribute('var_name')))
            ->repr($this->getAttribute('extension_name'))
            ->raw(");\n")
            ->write(
                sprintf(
                    '$%s->start($%s = new \Oro\TwigInspector\Twig\NodeReference(',
                    $this->getAttribute('var_name'),
                    $this->getAttribute('var_name').'_ref'
                )
            )
            ->repr($this->getAttribute('name'))
            ->raw(', $this->getTemplateName(), ')
            ->repr($this->getAttribute('line'))
            ->raw("));\n\n");
    }
}
