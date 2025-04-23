FROM php:8.2-apache

# คัดลอกทุกไฟล์จาก root ไปยัง /var/www/html/
COPY . /var/www/html/

EXPOSE 80
