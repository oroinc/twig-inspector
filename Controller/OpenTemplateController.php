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

    /** @var string */
    private $projectDir;

    /**
     * @param Environment       $twig
     * @param FileLinkFormatter $fileLinkFormatter
     * @param string            $projectDir
     */
    public function __construct(Environment $twig, FileLinkFormatter $fileLinkFormatter, string $projectDir)
    {
        $this->twig = $twig;
        $this->fileLinkFormatter = $fileLinkFormatter;
        $this->projectDir = $projectDir;
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

        // make file path relative (#5)
        $file = str_replace($this->projectDir . '/', '', $file);

        $url = $this->fileLinkFormatter->format($file, $line);

        // add possible sub directory (#5)
        $url = str_replace($request->getSchemeAndHttpHost() . '/', $request->getSchemeAndHttpHost() . $request->getBasePath() . '/', $url);

        // decode html entities to avoid wrong ampersands (&amp; -> &) (#5)
        $url = html_entity_decode($url);

        return new RedirectResponse($url);
    }
}
