<?php

namespace Oro\TwigInspector;

/**
 * Generates prefixed for start and end comment tags.
 */
class BoxDrawings
{
    protected const CHARSETS = [
        ['┏', '━', '┗'],
        ['╭', '─', '╰'],
        ['╔', '═', '╚'],
        ['┎', '─', '┖'],
    ];

    private int $charsetIndex = 0;

    private int $length = 0;

    public function getStartCommentPrefix(): string
    {
        $prefix = $this->getCharset()[0];
        $prefix .= str_repeat($this->getCharset()[1], $this->length);

        return $prefix;
    }

    public function getEndCommentPrefix(): string
    {
        $prefix = $this->getCharset()[2];
        $prefix .= str_repeat($this->getCharset()[1], $this->length);

        return $prefix;
    }

    public function blockChanged(int $length): void
    {
        $this->length = $length;
        $this->charsetIndex++;
        if ($length === 0 || count(self::CHARSETS) - 1 === $this->charsetIndex) {
            $this->charsetIndex = 0;
        }
    }

    private function getCharset(): array
    {
        return self::CHARSETS[$this->charsetIndex];
    }
}
