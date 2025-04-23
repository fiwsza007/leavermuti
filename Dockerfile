# ใช้ PHP + Apache
FROM php:8.2-apache

# คัดลอกไฟล์ทั้งหมดจากโฟลเดอร์ public ไปยัง root ของ Apache
COPY public/ /var/www/html/

# เปิดพอร์ต 80 (Render จะใช้พอร์ตนี้โดยอัตโนมัติ)
EXPOSE 80
