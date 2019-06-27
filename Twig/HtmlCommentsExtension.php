<?php

namespace Oro\TwigInspector\Twig;

use Oro\TwigInspector\BoxDrawings;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;

/**
 * Adds comments before and after every Twig block and template
 */
class HtmlCommentsExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    protected const ENABLE_FLAG_COOKIE_ID = 'twig_inspector_is_active';

    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $previousContent;

    /** @var int */
    private $nestingLevel = 0;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
            $boxDrawings = $this->container->get('oro_twig_inspector.box_drawings');

            if (strpos($content, $this->previousContent) !== false) {
                if (trim($content) !== trim($this->previousContent)) {
                    $boxDrawings->blockChanged($this->nestingLevel);
                }
                $this->nestingLevel++;
            } else {
                $this->nestingLevel = 0;
                $boxDrawings->blockChanged($this->nestingLevel);
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
        $request = $this->container->get('request_stack')
            ->getCurrentRequest();

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
        $prefix = $this->container->get('oro_twig_inspector.box_drawings')
            ->getStartCommentPrefix();

        return $this->getComment($prefix, $ref);
    }

    /**
     * @param NodeReference $ref
     * @return string
     */
    private function getEndComment(NodeReference $ref): string
    {
        $prefix = $this->container->get('oro_twig_inspector.box_drawings')
            ->getEndCommentPrefix();

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
        return $this->container->get('router')->generate(
            'oro_twig_inspector_template_link',
            [
                'template' => $ref->getTemplate(),
                'line' => $ref->getLine(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'request_stack' => RequestStack::class,
            'router' => UrlGeneratorInterface::class,
            'oro_twig_inspector.box_drawings' => BoxDrawings::class,
        ];
    }
}
