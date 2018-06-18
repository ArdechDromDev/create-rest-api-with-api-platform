FROM php:7.1.14-fpm

ARG USER_UID
ARG USER_GID
ARG USER_NAME
ARG USER_GROUP

RUN apt-get update \
	&& apt-get install -y libmcrypt-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libfreetype6-dev \
        git \
		libxml2-dev \
		libicu-dev \
		mysql-client \
		wget \
		unzip \
        curl \
        libfontconfig1 \
        libxrender1 \
        libmemcached-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install iconv intl mcrypt opcache pdo pdo_mysql mbstring soap gd zip \
    && pecl install mongodb && docker-php-ext-enable mongodb \
    && pecl install memcached && docker-php-ext-enable memcached \
    && apt-get autoremove -y --purge \
    && apt-get clean \
    && rm -Rf /tmp/*

RUN { \
	echo 'error_reporting=E_ALL;'; \
	echo 'display_errors=1;'; \
	echo 'html_errors=1;'; \
	echo 'short_open_tag=0;'; \
    echo 'memory_limit=4096M;'; \
    echo 'upload_max_filesize=64M;'; \
    echo 'post_max_size=64M;'; \
    echo 'max_input_vars=6000;'; \
    echo 'max_execution_time=180;'; \
	echo 'date.timezone=Europe/Paris;'; \
	echo 'default_charset = "utf-8";'; \
} > /usr/local/etc/php/conf.d/custom.ini

# Install APCu extension
ADD https://pecl.php.net/get/apcu-5.1.3.tgz /tmp/apcu.tar.gz
RUN mkdir -p /usr/src/php/ext/apcu\
  && tar xf /tmp/apcu.tar.gz -C /usr/src/php/ext/apcu --strip-components=1

# configure and install
RUN docker-php-ext-configure apcu\
  && docker-php-ext-install apcu

RUN rm -rd /usr/src/php/ext/apcu && rm /tmp/apcu.tar.gz

# Install APCu-BC extension
ADD https://pecl.php.net/get/apcu_bc-1.0.3.tgz /tmp/apcu_bc.tar.gz
RUN mkdir -p /usr/src/php/ext/apcu-bc\
  && tar xf /tmp/apcu_bc.tar.gz -C /usr/src/php/ext/apcu-bc --strip-components=1

# configure and install
RUN docker-php-ext-configure apcu-bc\
  && docker-php-ext-install apcu-bc

RUN rm -rd /usr/src/php/ext/apcu-bc && rm /tmp/apcu_bc.tar.gz

#Load APCU.ini before APC.ini
RUN rm /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini
RUN { \
	echo 'extension=apcu.so;'; \
	echo 'apc.enabled=1;'; \
	echo 'apc.enable_cli=0;'; \
	echo 'apc.stat=1;'; \
	echo 'apc.max_file_size="1M";'; \
} > /usr/local/etc/php/conf.d/20-php-ext-apcu.ini

RUN groupadd -g ${USER_UID} ${USER_GROUP} \
    && useradd -u "${USER_UID}" -g "${USER_GROUP}" -m -d "/home/${USER_NAME}" -s /bin/bash "${USER_NAME}" \
    && echo "${USER_GROUP}:${USER_NAME}" | chpasswd \
    && usermod -a -G sudo "${USER_NAME}"

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/
RUN mv /usr/bin/composer.phar /usr/bin/composer

# wkhtmltopdf
RUN curl "https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz" -L -o "wkhtmltopdf.tar.xz"
RUN tar Jxvf wkhtmltopdf.tar.xz
RUN mv wkhtmltox/bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf

RUN chown -R ${USER_GROUP}:${USER_NAME} /var/www
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html
USER ${USER_NAME}

CMD ["php-fpm"]
