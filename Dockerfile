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

# Install LiteFS
RUN apt-get install -y ca-certificates fuse3 sqlite3

COPY --from=flyio/litefs:0.5 /usr/local/bin/litefs /usr/local/bin/litefs

# Copy the application code
COPY . /app
COPY ./etc/fuse.conf /etc/litefs.yml

# Use the LiteFS entrypoint to use Fly.io Sqlite
ENTRYPOINT litefs mount
