FROM php:8.3-apache

# extension build dependencies
RUN apt-get update -y && apt-get install -y --no-install-recommends \
	libpng-dev \
	libjpeg-dev \
	libfreetype6-dev \
	libzip-dev \
	libbz2-dev \
	libc6-dev \
	libicu-dev \
	libxml2-dev \
	git \
	unzip \
	&& apt-get clean -y \
	&& rm -rf /var/lib/apt/lists/*

# install composer ( copied from the official composer image )
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# install php extensions
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install bz2
RUN docker-php-ext-install calendar
RUN docker-php-ext-install exif
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install ftp
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-install gettext
RUN docker-php-ext-install iconv
RUN docker-php-ext-install intl
#RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install soap
RUN docker-php-ext-install sockets
RUN docker-php-ext-install xml
RUN docker-php-ext-install zip

# enable php extensions
RUN docker-php-ext-enable bcmath
RUN docker-php-ext-enable bz2
RUN docker-php-ext-enable calendar
#RUN docker-php-ext-enable curl
RUN docker-php-ext-enable exif
RUN docker-php-ext-enable fileinfo
RUN docker-php-ext-enable ftp
RUN docker-php-ext-enable gd
RUN docker-php-ext-enable gettext
RUN docker-php-ext-enable iconv
#RUN docker-php-ext-enable mbstring
RUN docker-php-ext-enable mysqli
RUN docker-php-ext-enable soap
RUN docker-php-ext-enable sockets
RUN docker-php-ext-enable xml
RUN docker-php-ext-enable zip

# install xdebug
RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug

# enable xdebug
# RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# RUN echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# RUN echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# apache modules
RUN a2enmod rewrite headers ssl socache_shmcb

RUN openssl req -x509 -newkey rsa:4096 -keyout mycert.key -out mycert.crt -sha256 -days 3650 -nodes -subj "/C=XX/ST=StateName/L=CityName/O=CompanyName/OU=CompanySectionName/CN=CommonNameOrHostname"

RUN mv mycert.crt /etc/ssl/certs/mycert.crt
RUN mv mycert.key /etc/ssl/private/mycert.key

RUN sed -i '/SSLCertificateFile.*snakeoil\.pem/c\SSLCertificateFile \/etc\/ssl\/certs\/mycert.crt' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i '/SSLCertificateKeyFile.*snakeoil\.key/cSSLCertificateKeyFile /etc/ssl/private/mycert.key\' /etc/apache2/sites-available/default-ssl.conf

RUN a2ensite default-ssl