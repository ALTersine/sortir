<?php

namespace App\Twig\Extension;

use App\Util\LogoGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LogoCallerExtension extends AbstractExtension
{
    public function __construct(private readonly LogoGenerator $logo)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('rand_logo', [$this, 'getTheLogo']),
        ];
    }

    public function getTheLogo(): string
    {
        return $this->logo->selectOneLogoForTheApp();
    }
}
