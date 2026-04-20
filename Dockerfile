FROM php:8.1.34-trixie

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create a non-root user
RUN useradd -m -u 1000 -s /bin/bash app

RUN mkdir /app

WORKDIR /app

# Change ownership of the working directory
RUN chown -R app:app /app

# Switch to non-root user
USER app

CMD ["sleep", "infinity"]
