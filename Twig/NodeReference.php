<?php

namespace Oro\TwigInspector\Twig;

/**
 * Model for storing data required for referencing to the Twig Node source code
 */
class NodeReference
{
    /** @var string */
    private $name;

    /** @var string */
    private $template;

    /** @var int */
    private $line;

    /** @var string */
    private $id;

    /**
     * @param string $name
     * @param string $template
     * @param int    $line
     */
    public function __construct(string $name, string $template, int $line)
    {
        $this->id = uniqid('', false);
        $this->name = $name;
        $this->template = $template;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }
}
