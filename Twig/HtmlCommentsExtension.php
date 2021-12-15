<?php

namespace Oro\TwigInspector\Twig;

use Oro\TwigInspector\BoxDrawings;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;

/**
 * Adds comments before and after every Twig block and template
 */
class HtmlCommentsExtension extends AbstractExtension
{
    protected const ENABLE_FLAG_COOKIE_ID = 'twig_inspector_is_active';

    private ?string $previousContent = null;

    private int $nestingLevel = 0;

    private RequestStack $requestStack;

    private UrlGeneratorInterface $urlGenerator;

    private BoxDrawings $boxDrawings;

    private array $skipBlocks = [];
    private bool $skipped = false;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        BoxDrawings $boxDrawings,
        array $skipBlocks
    ) {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->boxDrawings = $boxDrawings;
        $this->skipBlocks = $skipBlocks;
    }

    public function start(NodeReference $ref): void
    {
        if (in_array($ref->getName(), $this->skipBlocks)) {
            $this->skipped = true;
        }
        if (!$this->isEnabled($ref)) {
            return;
        }
        ob_start();
    }

    public function end(NodeReference $ref): void
    {
        if (in_array($ref->getName(), $this->skipBlocks)) {
            $this->skipped = false;
            return;
        }
        if (!$this->isEnabled($ref)) {
            return;
        }

        $content = ob_get_clean();

        if ($this->isSupported($content)) {
            if ((string)$this->previousContent !== '' && strpos($content, $this->previousContent) !== false) {
                if (trim($content) !== trim($this->previousContent)) {
                    $this->boxDrawings->blockChanged($this->nestingLevel);
                }
                $this->nestingLevel++;
            } else {
                $this->nestingLevel = 0;
                $this->boxDrawings->blockChanged($this->nestingLevel);
            }

            $content = $this->getStartComment($ref).$content.$this->getEndComment($ref);

            $this->previousContent = $content;
        }
        echo $content;
    }

    protected function isEnabled(NodeReference $ref): bool
    {
        if ($this->skipped) {
            return false;
        }
        $request = $this->requestStack->getCurrentRequest();
        if (!$request || !$request->cookies->getBoolean(self::ENABLE_FLAG_COOKIE_ID)) {
            return false;
        }

        return '.html.twig' === substr($ref->getTemplate(), -10);
    }

    protected function isSupported(string $string): bool
    {
        // has HTML tags
        if ($string === strip_tags($string)) {
            return false;
        }

        // doesn't start with JSON open bracket (check works faster than json_decode and should be enough)
        if (\in_array(trim($string)[0], ['[', '{'], true)) {
            return false;
        }

        // not a backbone template
        if (false !== strpos($string, '<%')) {
            return false;
        }

        return true;
    }

    private function getStartComment(NodeReference $ref): string
    {
        $prefix = $this->boxDrawings->getStartCommentPrefix();

        return $this->getComment($prefix, $ref);
    }

    private function getEndComment(NodeReference $ref): string
    {
        $prefix = $this->boxDrawings->getEndCommentPrefix();

        return $this->getComment($prefix, $ref);
    }

    protected function getComment(string $prefix, NodeReference $ref): string
    {
        $link = $this->getLink($ref);

        return '<!-- '.$prefix.' '.$ref->getName().' ['.$link.'] #'.$ref->getId().'-->';
    }

    protected function getLink(NodeReference $ref): string
    {
        return $this->urlGenerator->generate(
            'oro_twig_inspector_template_link',
            [
                'template' => $ref->getTemplate(),
                'line' => $ref->getLine(),
            ]
        );
    }
}
