<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    // Utilise le trait pour une configuration minimale et rapide (micro-framework)
    use MicroKernelTrait;

    // Indique à Symfony où se trouve la racine du projet
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }
}
