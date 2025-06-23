FROM php:8.3-fpm-alpine

# Instala dependências do sistema e extensões PHP necessárias para Laravel
RUN apk add --no-cache \
    nginx \
    mysql-client \
    curl \
    libzip-dev \
    icu-dev \
    libpng-dev \
    jpeg-dev \
    nodejs \
    npm \
    git \
    oniguruma-dev \
    # Extensões PHP adicionais (pdo_mysql é para MySQL)
    && docker-php-ext-install pdo pdo_mysql zip bcmath intl exif gd \
    # Habilita a extensão opcache para melhor desempenho
    && docker-php-ext-enable opcache

# Copia o binário do Composer para dentro do contêiner
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

# Adiciona um usuário 'www-data' (comum em ambientes PHP) e define o seu UID/GID para compatibilidade
# Este passo é crucial para gerir permissões em sistemas Windows com bind mounts.
# RUN addgroup -g 82 -S www-data && adduser -u 82 -D -S -G www-data www-data

# Define o usuário que vai executar o PHP-FPM
# USER www-data

# Expõe a porta 9000 para PHP-FPM (usado pelo Nginx)
EXPOSE 9000

# Comando para iniciar o PHP-FPM quando o contêiner for executado
CMD ["php-fpm"]
