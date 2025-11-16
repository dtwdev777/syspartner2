<?php
// /var/www/html/scheduler_loop.php

// Используем подтвержденный рабочий путь к artisan
$artisan_path = '/var/www/html/app/artisan'; 
$php_binary = '/usr/local/bin/php';
$sleep_time = 60; // 60 секунд

// Основной цикл
while (true) {
    // Выполняем команду schedule:run
    exec("{$php_binary} {$artisan_path} schedule:run");

    // Ждем 60 секунд перед следующим запуском
    sleep($sleep_time);
}