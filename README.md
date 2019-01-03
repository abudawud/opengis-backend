## OpenGIS - Backend
Backend REST API untuk OpenGIS - React
<img src="https://github.com/abudawud/opengis-react/blob/master/doc/screenshoot.png">

## Getting Started
Untuk dapat menjalankan projek ini berikut yang harus disiapkan telebih dahulu
* PHP
* Mysql Server
* PHP Composer

## Deployment
Untuk menjalankan projek ini:
* Import DB-GIS yang ada di projek ini (gis-db.sql) ke server db anda
* Install dependensi projek dengan: composer install
* Sesuaikan konfigurasi yang ada di config/config.php dengan sistem anda

kemudian buka browser dengan alamat localhost

## Endpoint
Response dari API ini berupa data dengan format GEO Json (GSON)
* /region - Daftar kab/kota di jawa timur saja (anda dapat menyesuaikan querynya di index.php)
* /polygon/{region} - Data fitur polygon, region dapat berisi nama kota/kab
* /markers - Data fitur marker

## Last But Not Least
Projek ini masih banyak kekurangan sehingga saran dan masukkan terlebih lagi kontribusi sangat diharapkan :)
