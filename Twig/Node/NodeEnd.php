<?php

namespace Oro\TwigInspector\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Modify generated Twig template to call the `end` method of HtmlCommentsExtension extension
 */
class NodeEnd extends Node
{
    /**
     * {@inheritDoc}
     */
    public function __construct($varName)
    {
        parent::__construct([], ['var_name' => $varName]);
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write("\n")
            ->write(
                sprintf(
                    "\$%s->end(\$%s);\n\n",
                    $this->getAttribute('var_name'),
                    $this->getAttribute('var_name').'_ref'
                )
            );
    }
}
