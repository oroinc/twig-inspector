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

    /** @var string */
    private $previousContent;

    /** @var int */
    private $nestingLevel = 0;

    /** @var RequestStack */
    private $requestStack;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var BoxDrawings */
    private $boxDrawings;

    /**
     * @param RequestStack          $requestStack
     * @param UrlGeneratorInterface $urlGenerator
     * @param BoxDrawings           $boxDrawings
     */
    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        BoxDrawings $boxDrawings
    ) {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->boxDrawings = $boxDrawings;
    }

    /**
     * @param NodeReference $ref
     */
    public function start(NodeReference $ref): void
    {
        if (!$this->isEnabled($ref)) {
            return;
        }
        ob_start();
    }

    /**
     * @param NodeReference $ref
     */
    public function end(NodeReference $ref): void
    {
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

    /**
     * @param NodeReference $ref
     * @return bool
     */
    protected function isEnabled(NodeReference $ref): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$request->cookies->getBoolean(self::ENABLE_FLAG_COOKIE_ID)) {
            return false;
        }

        return '.html.twig' === substr($ref->getTemplate(), -10);
    }

    /**
     * @param string $string
     * @return bool
     */
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

    /**
     * @param NodeReference $ref
     * @return string
     */
    private function getStartComment(NodeReference $ref): string
    {
        $prefix = $this->boxDrawings->getStartCommentPrefix();

        return $this->getComment($prefix, $ref);
    }

    /**
     * @param NodeReference $ref
     * @return string
     */
    private function getEndComment(NodeReference $ref): string
    {
        $prefix = $this->boxDrawings->getEndCommentPrefix();

        return $this->getComment($prefix, $ref);
    }

    /**
     * @param string        $prefix
     * @param NodeReference $ref
     * @return string
     */
    protected function getComment(string $prefix, NodeReference $ref): string
    {
        $link = $this->getLink($ref);

        return '<!-- '.$prefix.' '.$ref->getName().' ['.$link.'] #'.$ref->getId().'-->';
    }

    /**
     * @param NodeReference $ref
     * @return string
     */
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
