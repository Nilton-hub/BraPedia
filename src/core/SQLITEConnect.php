<?php

namespace src\core;

use PDO;
use PDOException;

final class SQLITEConnect
{
    private static ?PDOException $error;
    private static ?PDO $connect;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return PDO|null
     */
    public static function connect(): ?PDO
    {
        if (empty(self::$connect)) {
            try {
                self::$connect = new PDO('sqlite:'.dirname(__DIR__, 2).'/data/var/database.sqlite');
                self::$connect->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                self::$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
//            self::$connect->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            } catch (PDOException $exception) {
                self::$error = $exception;
                return null;
            }
        }
        return self::$connect;
    }

    /**
     * @return PDOException|null
     */
    public static function error()
    {
        return self::$error;
    }
}
