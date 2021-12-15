<?php

namespace Oro\TwigInspector\Twig;

/**
 * Model for storing data required for referencing to the Twig Node source code
 */
class NodeReference
{
    private string $name;

    private string $template;

    private int $line;

    private string $id;

    public function __construct(string $name, string $template, int $line)
    {
        $this->id = uniqid('', false);
        $this->name = $name;
        $this->template = $template;
        $this->line = $line;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
