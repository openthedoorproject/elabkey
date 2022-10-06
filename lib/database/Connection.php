<?php
    // conexão com o banco de dados
    require_once(__DIR__ . "/../../vendor/autoload.php");

    $envFileDir = __DIR__ . "/../../";

    Dotenv\Dotenv::createImmutable($envFileDir)->load();

    abstract class Connection{
        private static $conn;

        public static function getConn(){
            if(self::$conn == null){
                try{
                    $host = $_ENV["DB_HOST"];
                    $port = $_ENV["DB_PORT"];
                    $database = $_ENV["DB_NAME"];
                    $user = $_ENV["DB_USER"];
                    $password = $_ENV["DB_PASSWORD"];
                    self::$conn = new PDO("mysql:host=$host;port=$port;dbname=$database;", $user, $password); // mudar de acordo com a configuração do BD
                } catch(Exception $e){
                   die($e->getMessage());
                }

            }
            return self::$conn;
        }
    }
