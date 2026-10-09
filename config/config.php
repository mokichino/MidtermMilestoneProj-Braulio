<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'sugar_nest';
const DB_USER = 'root';
const DB_PASS = '';
const BASE_URL = '/MidtermMilestoneProj-Braulio';

$secureCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $secureCookie,
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}
