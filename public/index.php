<?php

use App\Kernel;

// Ce fichier est le point d'entrée unique (front controller) pour toutes les requêtes HTTP.

// Vérifie si les dépendances de Composer ont été chargées.
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Le front controller utilise la fonction `composer_require_env`
// pour obtenir le nom de la classe Kernel à utiliser (souvent App\Kernel).
// Elle renvoie une fonction qui crée et démarre le Kernel de Symfony.
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
