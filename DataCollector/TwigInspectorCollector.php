<?php

namespace Oro\TwigInspector\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Empty data collector, required in order to add an icon to Symfony Debug Toolbar
 */
class TwigInspectorCollector implements DataCollectorInterface
{
    #[\Override]
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    #[\Override]
    public function reset(): void
    {
    }

    #[\Override]
    public function getName(): string
    {
        return 'twig_inspector';
    }
}
