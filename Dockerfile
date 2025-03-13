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
    sodium \
    pcntl \
    sockets \
    bcmath

# Install Python "UV" package manager (https://github.com/astral-sh/uv)
RUN curl -LsSf https://astral.sh/uv/install.sh | sh

# Install libmagic (for Python Mimetype detection)
RUN apt-get update -y && apt-get install -y \
    libmagic-dev \
    unzip \
    git \
    unzip \
    libpq-dev \
    supervisor \
    libreoffice \
    libreoffice-java-common \
    unoconv \
    build-essential \
    pkg-config \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libopenjp2-7-dev \
    libtiff-dev \
    libharfbuzz-dev \
    libfribidi-dev \
    libglu1-mesa-dev \
    libxcursor-dev \
    libxrandr-dev \
    libxinerama-dev \
    libxi-dev \
    libcairo2-dev \
    libgirepository1.0-dev \
    libffi-dev \
    python3-dev \
    python3-pip \
    python3-setuptools \
    python3-wheel \
    tesseract-ocr

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install bun
RUN curl -fsSL https://bun.sh/install | bash


# Copy the application code
COPY . /app
COPY ./etc/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .env.docker /app/.env

RUN mkdir -p /app/database
RUN touch /app/database/database.sqlite

COPY ./etc/php.ini /usr/local/etc/php/php.ini

RUN composer install --no-dev --no-interaction --no-progress --no-suggest

RUN php artisan storage:link
RUN php artisan migrate --force
RUN #php artisan filament:cache-components

# RUN cd /app/vendor/mateffy/llm-magic/python
WORKDIR /app/vendor/mateffy/llm-magic/python
RUN uv add mupdf
WORKDIR /app

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
