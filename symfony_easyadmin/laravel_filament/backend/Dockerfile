# --- backend/Dockerfile ---
# Imagem base PHP 8.3 com FPM, baseada em Alpine para um tamanho menor
FROM php:8.3-fpm-alpine

# Argumentos para IDs de usuário e grupo (para corrigir permissões de arquivos)
ARG UID=1000
ARG GID=1000

# Variáveis de ambiente
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME="/tmp/composer"

# Instala dependências do sistema e extensões PHP necessárias para Symfony
# - icu-dev: para a extensão intl (internacionalização)
# - libzip-dev: para a extensão zip
# - postgresql-dev ou mariadb-dev: para pdo_pgsql ou pdo_mysql
# - git, unzip: para o Composer
# - Outras extensões úteis: opcache, apcu (para cache)
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    # Para MySQL (substitua por postgresql-dev se usar PostgreSQL)
    mariadb-dev \
    # Dependências para outras extensões comuns
    libxml2-dev \
    oniguruma-dev # Para mbstring
    # Adicione outras dependências do sistema aqui se necessário

# Instala as extensões PHP
RUN docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) intl pdo pdo_mysql zip opcache mbstring xml
    # Para APCu (cache):
    # RUN pecl install apcu && docker-php-ext-enable apcu

# Instala o Composer (gerenciador de dependências para PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria um usuário e grupo não-root para rodar a aplicação
# Isso é uma boa prática de segurança e ajuda com permissões de arquivo
RUN addgroup -g ${GID} --system appgroup && \
    adduser -u ${UID} --system -G appgroup -s /bin/bash appuser

# Define o diretório de trabalho
WORKDIR /srv/app

# Copia o código da aplicação para o diretório de trabalho
# Faremos isso no docker-compose.yml usando volumes para desenvolvimento,
# mas esta linha é útil se você construir uma imagem para produção.
# COPY . /srv/app

# Altera o proprietário dos arquivos para o usuário não-root
# Isso também será mais relevante para produção ou se não usar volumes de desenvolvimento.
# RUN chown -R appuser:appgroup /srv/app

# Muda para o usuário não-root
USER appuser

# Expõe a porta 9000 para o PHP-FPM
EXPOSE 9000

# O comando padrão será definido no docker-compose.yml (geralmente 'php-fpm')
# CMD ["php-fpm"]
