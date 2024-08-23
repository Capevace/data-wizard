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

COPY . /app
