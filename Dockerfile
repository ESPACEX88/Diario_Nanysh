FROM php:8.3-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de dependencias primero (para cache de Docker)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias de PHP (sin scripts que requieren artisan)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Instalar dependencias de Node
RUN npm install --legacy-peer-deps

# Copiar el resto de los archivos (incluyendo artisan)
COPY . .

# Ejecutar scripts de Composer ahora que artisan existe
RUN composer dump-autoload --optimize --no-interaction

# Compilar assets
RUN npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Variables de entorno (se pueden sobrescribir)
ENV PORT=8000

# Crear script de inicio
RUN echo '#!/bin/sh\n\
set -e\n\
\n\
# Crear enlace simbólico de storage si no existe\n\
if [ ! -L public/storage ]; then\n\
    php artisan storage:link\n\
fi\n\
\n\
# Ejecutar migraciones\n\
php artisan migrate --force\n\
\n\
# Iniciar servidor\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT}\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

# Comando para iniciar
CMD ["/usr/local/bin/start.sh"]

