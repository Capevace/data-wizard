FROM mateffy/data-wizard:latest

ENTRYPOINT ["php", "/app/artisan", "wizard"]
CMD ["--help"]
