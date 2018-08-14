<?php

namespace Oro\TwigInspector\Twig\Node;

use Twig_Compiler;
use Twig_Node;

/**
 * Modify generated Twig template to call the `end` method of HtmlCommentsExtension extension
 */
class NodeEnd extends Twig_Node
{
    /**
     * {@inheritDoc}
     */
    public function __construct($varName)
    {
        parent::__construct([], ['var_name' => $varName]);
    }

    /**
     * @param Twig_Compiler $compiler
     */
    public function compile(Twig_Compiler $compiler)
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
