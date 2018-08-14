<?php

namespace Oro\TwigInspector\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\FileLinkFormatter;
use Twig\Environment;

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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, string $template)
    {
        $line = $request->query->get('line', 1);

        /** @var \Twig_Template $template */
        $template = $this->twig->loadTemplate($template);
        $file = $template->getSourceContext()->getPath();

        $url = $this->fileLinkFormatter->format($file, $line);

        return new RedirectResponse($url);
    }
}
