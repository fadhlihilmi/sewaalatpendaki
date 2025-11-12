<?php
// config.php - konfigurasi koneksi MySQL
// Sesuaikan DB_HOST / DB_NAME / DB_USER / DB_PASS sesuai environment (XAMPP biasanya user root tanpa password)
define('DB_HOST','127.0.0.1');
define('DB_NAME','sewa_db');
define('DB_USER','root');
define('DB_PASS','');

function getPDO(){
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
