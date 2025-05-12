# Base image
FROM php:8.2-fpm

# Set arguments for user and group IDs
ARG USER_UID=1000
ARG USER_GID=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    libxml2-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 18.x
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        bcmath \
        zip \
        opcache \
        pcntl

# Install phpredis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Create the user (www) matching host's UID/GID
RUN groupadd -g ${USER_GID} www \
    && useradd -u ${USER_UID} -g www -m www \
    && usermod -a -G www-data www

# Set working directory
WORKDIR /var/www

# Copy project files (optional if you want build-time copy)
COPY --chown=www:www . /var/www

# Set permissions
RUN chown -R www:www /var/www

# Switch to the new user
USER www

# Expose port and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
