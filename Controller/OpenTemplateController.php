<?php

namespace Oro\TwigInspector\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Open Twig template in an IDE by template name at the the line
 */
class OpenTemplateController
{
    private Environment $twig;

    private FileLinkFormatter $fileLinkFormatter;

    public function __construct(Environment $twig, FileLinkFormatter $fileLinkFormatter)
    {
        $this->twig = $twig;
        $this->fileLinkFormatter = $fileLinkFormatter;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Request $request, string $template): RedirectResponse
    {
        $line = $request->query->get('line', 1);

        /** @var TemplateWrapper $template */
        $template = $this->twig->load($template);
        $file = $template->getSourceContext()->getPath();

        $url = $this->fileLinkFormatter->format($file, $line);

        return new RedirectResponse($url);
    }
}
