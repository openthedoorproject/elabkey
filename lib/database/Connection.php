<?php
    // conexão com o banco de dados
    abstract class Connection{
        private static $conn;

        public static function getConn(){
            if(self::$conn == null){
                try{
                    self::$conn = new PDO('mysql:host=127.0.0.1;dbname=387930;', '387930', "tccIfsElabkey"); // mudar de acordo com a configuração do BD
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit();
                    self::$conn = new PDO('mysql:host=127.0.0.1;port=3308;dbname=a;', 'root', '');
                }

            }
            return self::$conn;
        }
    }
