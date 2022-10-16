FROM alpine:3.16


RUN apk update \
    && apk upgrade \
    && apk add apache2 php81 php81-apache2 php81-curl php81-iconv php81-mbstring php81-json php81-openssl php81-zip php81-pdo php81-pdo_sqlite php81-dom wireguard-tools sudo

RUN echo "apache ALL=(root) NOPASSWD: /sbin/ip" > /etc/sudoers.d/apache
RUN echo "apache ALL=(root) NOPASSWD: /usr/bin/wg" >> /etc/sudoers.d/apache

# DEVELOP MODE
RUN apk add vim mc


#RUN rm /var/www/localhost/htdocs/index.html
#COPY index.php /var/www/localhost/htdocs/index.php
COPY httpd.conf /etc/apache2/httpd.conf
COPY init.sh /init.sh

WORKDIR /app
CMD /init.sh

EXPOSE 80
