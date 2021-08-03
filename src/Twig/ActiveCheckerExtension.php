<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ActiveCheckerExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('active', [$this, 'matchUrlWithCurrent']),
        ];
    }

    public function matchUrlWithCurrent(string $value): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (
            $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                $request->attributes->get('_route_params')
            ) == $this->urlGenerator->generate($value)
        ) {
            return ' active';
        } else {
            return '';
        }
    }
}
