<?php

namespace Oro\TwigInspector\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\FileLinkFormatter;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Open Twig template in an IDE by template name at the the line
 */
class OpenTemplateController
{
    /** @var Environment */
    private $twig;

    /** @var FileLinkFormatter */
    private $fileLinkFormatter;

    /**
     * @param Environment       $twig
     * @param FileLinkFormatter $fileLinkFormatter
     */
    public function __construct(Environment $twig, FileLinkFormatter $fileLinkFormatter)
    {
        $this->twig = $twig;
        $this->fileLinkFormatter = $fileLinkFormatter;
    }

    /**
     * @param Request $request
     * @param string  $template
     * @return RedirectResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Request $request, string $template)
    {
        $line = $request->query->get('line', 1);

        /** @var TemplateWrapper $template */
        $template = $this->twig->load($template);
        $file = $template->getSourceContext()->getPath();

        $url = $this->fileLinkFormatter->format($file, $line);

        return new RedirectResponse($url);
    }
}
