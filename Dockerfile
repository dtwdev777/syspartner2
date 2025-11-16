FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - 
RUN curl -fsSL https://deb.nodesource.com/setup_20.x |  bash -
RUN  apt-get install -y nodejs
RUN npm install -g npm@10.8.1
RUN apt-get update && apt-get install -y supervisor
RUN apt-get update && apt-get -y install cron
# Expose port 9000 and start php-fpm server
ADD crontab /etc/cron.d/task
RUN chmod 0644 /etc/cron.d/task
EXPOSE 9000
#"php-fpm"
CMD [ "/usr/bin/supervisord"]