FROM thecodingmachine/php:7.4-v3-apache

COPY --chown=docker:docker . .

# Docker image
ENV APACHE_DOCUMENT_ROOT="public/"
ENV PHP_EXTENSION_GD="1"
ENV PHP_INI_MEMORY_LIMIT="1G"
ENV PHP_INI_UPLOAD_MAX_FILESIZE="10M"
ENV PHP_INI_SESSION__GC_MAXLIFETIME="86400"

RUN composer install