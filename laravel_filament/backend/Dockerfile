# Imagem base PHP 8.3 com FPM, baseada em Alpine para um tamanho menor
FROM php:8.3-fpm-alpine

# Argumentos para IDs de usuário e grupo (para corresponder ao seu usuário local)
ARG UID=1000
ARG GID=1000

# Variáveis de ambiente
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME="/tmp/composer"
ENV PATH="/var/www/html/vendor/bin:$PATH"

RUN apk add --no-cache vim

# Instala dependências do sistema e extensões PHP necessárias para Laravel
# Adiciona Node.js LTS, NPM e a extensão PHP Redis
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    # Dependências para extensões PHP
    icu-dev \
    libzip-dev \
    libxml2-dev \
    # Para mbstring
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Dependências para pdo_mysql
    mariadb-dev \
    # Dependências para pcntl (usado em filas, Horizon)
    linux-headers \
    # Outras utilidades
    curl \
    supervisor \
    # Adiciona Node.js (versão LTS é geralmente uma boa escolha) e npm
    nodejs-current \
    npm \
    # Dependências para compilar a extensão Redis PECL
    $PHPIZE_DEPS

# Instala as extensões PHP
# pcntl é útil para Laravel Horizon e gerenciamento de filas
# exif é frequentemente necessário para manipulação de metadados de imagem
# gd para manipulação de imagens
# redis para integração com o serviço Redis
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    intl \
    zip \
    pdo \
    pdo_mysql \
    bcmath \
    opcache \
    mbstring \
    xml \
    exif \
    pcntl \
    gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Instala o Composer (gerenciador de dependências para PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria um usuário e grupo não-root para rodar a aplicação (ex: 'appuser')
# Isso ajuda com permissões de arquivo, especialmente em desenvolvimento
RUN addgroup -g ${GID} --system appuser && \
    adduser -u ${UID} --system -G appuser -s /bin/sh -D appuser
    # O -D significa sem senha, -s /bin/sh para um shell simples

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o código da aplicação para o diretório de trabalho
# Em desenvolvimento, isso é geralmente sobrescrito por um volume mount no docker-compose.yml
# Mas é bom ter para construir uma imagem de produção.
COPY . .

# --- Opcional: Passos para build de frontend ---
# Se você quiser que os assets de frontend sejam construídos durante o build da imagem Docker
# (mais comum para imagens de produção):
# 1. Copie os arquivos package.json e package-lock.json (ou yarn.lock)
# COPY package.json package-lock.json ./
#
# 2. Instale as dependências do Node.js
# RUN npm install
#
# 3. Copie o restante dos arquivos de frontend (se não foram copiados pelo COPY . . acima)
# COPY postcss.config.js tailwind.config.js vite.config.js ./
# COPY resources/css ./resources/css
# COPY resources/js ./resources/js
#
# 4. Compile os assets de frontend
# RUN npm run build
# --- Fim dos passos opcionais de build de frontend ---

# Altera o proprietário dos arquivos para o usuário não-root
# Isso garante que o usuário da aplicação possa escrever nos diretórios necessários (storage, bootstrap/cache)
# Este comando será mais eficaz se executado após o código ser copiado ou montado.
# Pode ser necessário rodar como root antes de mudar para USER appuser,
# ou ajustar permissões no entrypoint/comando do docker-compose.
# RUN chown -R appuser:appgroup /var/www/html # Exemplo

# Muda para o usuário não-root (opcional, mas boa prática para segurança)
# Se você descomentar esta linha, certifique-se de que o PHP-FPM e
# quaisquer comandos subsequentes possam ser executados como este usuário.
# USER appuser

# Expõe a porta 9000 para o PHP-FPM
EXPOSE 9000
EXPOSE 8000
EXPOSE 5173

# O comando padrão será 'php-fpm', que é o default da imagem base php:fpm-alpine
# CMD ["php-fpm"]
CMD ["composer", "run", "dev"]
