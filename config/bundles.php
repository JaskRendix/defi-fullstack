<?php

return [
    // FrameworkBundle est le cœur de Symfony
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    
    // MakerBundle est utile pour générer du code (utilisé seulement en dev)
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];
