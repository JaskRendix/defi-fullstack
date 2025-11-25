# Utilise l'image PHP 8.2 FPM (FastCGI Process Manager) basée sur Alpine Linux pour la légèreté.
FROM php:8.2-fpm-alpine

# Arguments de construction
ARG UID=1000
ARG GID=1000

# Mettre à jour les paquets et installer les dépendances du système et les extensions PHP via Alpine
RUN apk update && apk add --no-cache \
    git \
    make \
    unzip \
    libxml2-dev \
    icu-dev \
    gettext-dev \
    mysql-client \
    sqlite-dev \
    \
    # Extensions PHP précompilées d'Alpine pour éviter les erreurs de compilation (notamment iconv)
    php82-iconv \
    php82-xml \
    php82-pdo_sqlite \
    php82-pdo_mysql \
    php82-opcache \
    php82-ctype \
    \
    # Nettoyage des fichiers temporaires après l'installation
    && rm -rf /var/cache/apk/*

# Installer Composer globalement
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Créer un utilisateur non-root pour la sécurité et l'aligner sur l'UID/GID de l'hôte
RUN addgroup -g $GID appuser && adduser -u $UID -G appuser -s /bin/sh -D appuser
# Donner la propriété de /var/www/html à l'utilisateur de l'application
RUN chown -R appuser:appuser /var/www/html
# Définir l'utilisateur par défaut pour l'exécution
USER appuser

# Définir le répertoire de travail
WORKDIR /var/www/html

# Le conteneur FPM écoute sur le port 9000
EXPOSE 9000

# Commande par défaut (démarrer PHP-FPM)
CMD ["php-fpm"]
