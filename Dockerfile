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
	opcache \
    ffi \
    imagick \
    sodium

# Install Python "UV" package manager (https://github.com/astral-sh/uv)
RUN curl -LsSf https://astral.sh/uv/install.sh | sh

# Install libmagic (for Python Mimetype detection)
RUN apt-get update -y && apt-get install -y libmagic-dev unzip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install bun
RUN curl -fsSL https://bun.sh/install | bash

# Copy the application code
COPY . /app

RUN composer install --no-dev --no-interaction --no-progress --no-suggest

RUN php artisan storage:link
RUN php artisan migrate --force
RUN php artisan optimize
RUN php artisan filament:cache-components
