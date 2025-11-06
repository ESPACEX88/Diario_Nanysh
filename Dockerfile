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

# Verificar que los assets se compilaron correctamente
RUN ls -la public/build/ || echo "Warning: build directory not found"

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
# Esperar un momento para que las variables de entorno estén disponibles\n\
sleep 2\n\
\n\
# Limpiar cache de configuración\n\
php artisan config:clear\n\
php artisan cache:clear\n\
\n\
# Crear enlace simbólico de storage si no existe\n\
if [ ! -L public/storage ]; then\n\
    php artisan storage:link\n\
fi\n\
\n\
# Verificar que la base de datos esté configurada antes de migrar\n\
if php artisan db:show 2>/dev/null || [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then\n\
    echo "Running migrations..."\n\
    php artisan migrate --force\n\
else\n\
    echo "Warning: Database not configured, skipping migrations"\n\
fi\n\
\n\
# Iniciar servidor\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT}\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

# Comando para iniciar
CMD ["/usr/local/bin/start.sh"]

