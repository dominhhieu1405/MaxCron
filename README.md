# MaxCron
Tool cron siêu nhanh, tối thiểu 1s

# Hướng dẫn dùng tool MaxCron
> Chạy được trên Linux & Termux

##  Bước 1: Cài đặt cập nhật, cài đặt các  gói cần thiết php
```base
apt update -y && apt upgrade -y && apt install php && apt install git && apt install toilet
```
## Bước 2: Clone tool về
```base
git clone https://github.com/dominhhieu1405/MaxCron.git && cd MaxCron
```
## Bước 3: Chạy tool
```base
php cron.php
```

# Một số lệnh cron
## Cron file php
```base
/usr/local/bin/php /home/useraname/public_html/path/to/cron/script.php
```
## Cron file phiên bản php tùy chỉnh
Thay <b>XX</b> ở <b>ea-phpXX</b> thành mã phiên bản php. VD: php 8.1 =>  ea-php8.1
```base
/usr/local/bin/ea-phpXX /home/useraname/public_html/path/to/cron/script.php
```
## Cron file phiên bản php tùy chỉnh
Thay <b>https://domain.com/</b> thành url cần cron
```base
curl -XGET 'https://domain.com/'
```
