#!/bin/bash

#mkdir /data
#mkdir /app
php81 artisan init
httpd -D FOREGROUND
