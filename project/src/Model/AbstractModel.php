<?php
namespace App\Model;

use PDO;
use PDOException;

abstract class AbstractModel
{
    private static $pdo;
    private static $host;
    private static $port;
    private static $dbname;
    private static $user;
    private static $password;
    private static $charset;

   // Connexion à la base de donnée
   private static function setBdd()
   {
       $config = parse_ini_file(__DIR__ . '/../../conf/db.ini');

       self::$host = $config['DB_HOST'];
       self::$port = $config['DB_PORT'];
       self::$dbname = $config['DB_NAME'];
       self::$user = $config['DB_USER'];
       self::$password = $config['DB_PASSWORD'];
       self::$charset = $config['DB_CHARSET'];

       // Connexion PDO

       $dsn = "mysql:dbname=" . self::$dbname . ";host=" . self::$host . ":" . self::$port . ";charset=utf8mb4";
       var_dump($dsn, self::$user, self::$password); 
       try {
         self::$pdo = new PDO($dsn);
         var_dump(self::$pdo);
       } catch (PDOException $ex) {
         echo "Erreur lors de la connexion à la base de données : " . $ex->getMessage();
         exit;
       }
   }

   // accès à la base de données
   protected function getBdd()
   {
    if(self::$pdo === null)
    {
        self::setBdd();
    }
    return self::$pdo;
   }
}

