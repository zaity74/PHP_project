FROM php:8.1-apache

ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    zip \
    wget \
    libssl-dev \
    libffi-dev

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install SQL Server Driver
RUN apt-get update && apt-get install -y \
    gnupg \
    gnupg2 \
    unixodbc-dev \
    freetds-dev \
    && apt-get update && apt-get install -y \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql

RUN apt-get update && \
    pecl install sqlsrv && \
    pecl install pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI
# RUN wget https://get.symfony.com/cli/installer -O - | bash
# RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Set working directory
WORKDIR /var/www/project

## Symfony cache folder
RUN groupadd dev -g 999
RUN useradd dev -g dev -d /var/www/project -m
RUN chown -R dev:dev /var/www/project

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]