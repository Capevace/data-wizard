[program:octane]
priority=5
command=php -d variables_order=EGPCS /var/www/html/artisan octane:start --server=frankenphp --admin-port=2019 --port=8000
user=www-data
autostart=true
autorestart=true
stdout_events_enabled=true
stderr_events_enabled=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
