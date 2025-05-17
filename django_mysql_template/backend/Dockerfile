# --- backend/Dockerfile ---
# Define a imagem Docker para a aplicação Django

# Imagem base Python (versão recente e slim)
FROM python:3.12-slim-bookworm

# Variáveis de ambiente para Python
ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1

# Define o diretório de trabalho dentro do contêiner
WORKDIR /app

# Instala dependências do sistema necessárias para mysqlclient e outras
# Para Debian Bookworm (base do python:3.12-slim), libmysqlclient-dev está disponível
# É importante rodar apt-get update antes de install
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    gcc \
    libmariadb-dev \
    # libmariadb-dev-compat também é uma opção se libmariadb-dev sozinho não funcionar
    pkg-config && \
    # Limpa o cache do apt para reduzir o tamanho da imagem
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copia o arquivo de dependências Python
COPY requirements.txt .

# Instala as dependências Python
# Usar --no-cache-dir reduz o tamanho da imagem
RUN pip install --no-cache-dir -r requirements.txt

# Copia o restante do código da aplicação para o diretório de trabalho no contêiner
# Isso deve ser feito após a instalação das dependências para aproveitar o cache do Docker
COPY . .

# (Opcional) Expõe a porta que o Django/Gunicorn irá rodar
# Esta linha é mais para documentação quando se usa docker-compose,
# pois o mapeamento de portas é feito no docker-compose.yml.
# EXPOSE 8000

# O comando para rodar a aplicação será definido no docker-compose.yml.
# Alternativamente, você pode usar um entrypoint.sh para tarefas mais complexas
# como esperar o banco de dados, rodar migrações, etc., antes de iniciar o servidor.
