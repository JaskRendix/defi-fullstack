<?php

// tests/bootstrap.php

// 1. Charger l'autoloading des dépendances (via composer)
require dirname(__DIR__).'/vendor/autoload.php';

// 2. Charger les variables d'environnement (.env) pour que les tests y aient accès
use Symfony\Component\Dotenv\Dotenv;

if (file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
