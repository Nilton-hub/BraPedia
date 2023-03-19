<?php

// BASE URL
const CONF_BASE_URL = 'http://localhost:8888';

// DATABASE
const CONF_PDO_OPT = [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

define('CONF_DB_HOST', getenv('CONF_DB_HOST'));
define('CONF_DB_DRIVER', getenv('CONF_DB_DRIVER'));
define('CONF_DB_PORT', getenv('CONF_DB_PORT'));
define('CONF_DB_USER', getenv('CONF_DB_USER'));
define('CONF_DB_PASS', getenv('CONF_DB_PASS'));
define('CONF_DB_NAME', getenv('CONF_DB_NAME'));

// PASSWORD
const CONF_PASSWD_MIN_LEN = 8;
const CONF_PASSWD_MAX_LEN = 40;
const CONF_PASSWD_ALGO = PASSWORD_DEFAULT;
const CONF_PASSWD_OPTIONS = ["cost" => 10];

// SESSION
define('SESSION_PATH', dirname(__DIR__, 2) . '/storage/sessions/');

// DATE
const CONF_DATE_BR = 'd/m/Y H:i:s';
const CONF_DATE_APP = 'Y-m-d H:i:s';

// STORAGE / IMAGES
const CONF_UPLOAD_DIR = __DIR__ . '/../../public/uploads';

// EMAIL
define('CONF_EMAIL', [
    'from_email' => getenv('FROM_EMAIL'),
    'from_name' => 'Nilton Duarte',
    'host' => getenv('HOST'),
    'auth'=> true,
    'user'=> getenv('USER'),
    'html' => true,
    'password' => getenv('PASSWORD'),
    'charset' => 'utf-8',
    'lang' => 'br',
    'secure' => \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    'port' => 587
]);

// CAPTCHA
define('CONF_CAPTCHA_URL', getenv('CONF_CAPTCHA_URL'));
define('CONF_CAPTCHA_SITEKEY', getenv('CONF_CAPTCHA_SITEKEY'));
define('CONF_CAPTCHA_SECRET', getenv('CONF_CAPTCHA_SECRET'));
