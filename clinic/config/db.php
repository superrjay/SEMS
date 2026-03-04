<?php
declare(strict_types=1);
namespace Clinic\Config;
use PDO; use PDOException; use RuntimeException;

class Database {
    private static ?PDO $connection = null;
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'clinic_db';
    private const DB_CHARSET = 'utf8mb4';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const PDO_OPTIONS = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_STRINGIFY_FETCHES=>false,PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"];
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try { $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', self::DB_HOST, self::DB_NAME, self::DB_CHARSET);
                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, self::PDO_OPTIONS);
            } catch (PDOException $e) { $ld=__DIR__.'/../logs'; if(!is_dir($ld))mkdir($ld,0755,true); error_log("[".date('Y-m-d H:i:s')."] DB: ".$e->getMessage().PHP_EOL,3,$ld.'/db_errors.log'); throw new RuntimeException('Database unavailable.'); }
        } return self::$connection;
    }
    public static function closeConnection(): void { self::$connection = null; }
}
function db(): PDO { return Database::getConnection(); }
