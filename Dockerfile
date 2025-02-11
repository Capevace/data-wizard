FROM dunglas/frankenphp
LABEL authors="Lukas Mateffy <hey@mateffy.me>"

RUN install-php-extensions \
	pdo_sqlite \
    pdo_pgsql \
	mbstring \
    exif \
	gd \
	intl \
	zip \
	opcache

# Install Python "UV" package manager (https://github.com/astral-sh/uv)
RUN curl -LsSf https://astral.sh/uv/install.sh | sh

# Install libmagic (for Python Mimetype detection)
RUN apt-get update -y && apt-get install -y libmagic-dev

# Copy the application code
COPY . /app
