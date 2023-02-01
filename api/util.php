<?php

use Dotenv\Dotenv;

    require_once("../vendor/autoload.php");

    function loadEnviroment() {
        $loader = Dotenv::createImmutable(__DIR__ . "/../");
        $loader->safeLoad();

        $loader->required("DB_HOST");
        $loader->required("DB_USER");
        $loader->required("DB_NAME");
        $loader->required("DB_PASSWORD");
    }

    function connect() {
        $hostname = $_ENV["DB_HOST"];
        $database = $_ENV["DB_NAME"];
        $username = $_ENV["DB_USER"];
        $password = $_ENV["DB_PASSWORD"];

        return mysqli_connect($hostname, $username, $password, $database);
    }

    function postToJSON() {
        $postData = file_get_contents("php://input");
        return json_decode($postData, true);
    }

    function registerAccess($authorization, $labNumber, $conn=null) {
        if($conn == null)
            $conn = connect();

        $insertSql = "insert into Registro_Acesso (Autorizacao_cod_autorizacao, Data_acesso, Hora_acesso, Laboratorio) 
                            values ($authorization, CURDATE(), CURTIME(), $labNumber);";

        return mysqli_query($conn, $insertSql);
    }

    function registerUserAccess($user, $labNumber, $conn = null) {
        if($conn == null)
            $conn = connect();

        $insertSql = "insert into Registro_Acesso (Usuario_matricula, Data_acesso, Hora_acesso, Laboratorio) 
                            values ($user, CURDATE(), CURTIME(), $labNumber);";

        return mysqli_query($conn, $insertSql);   
    }

    function authorizationCodeForPassword($password, $labNumber, $conn=null) {
        if($conn == null) 
            $conn = connect();

        $selectSql = "select Cod_autorizacao from Autorizacao where Senha = \"$password\" and Laboratorio = $labNumber 
                        and CURDATE() = Data_validade and CURTIME() between Hora_inicial and Hora_final;";

        $result = mysqli_query($conn, $selectSql);
        
        $authorizationCode = null;

        if($result->num_rows > 0) {
            $authorization = $result->fetch_assoc();
            $authorizationCode = $authorization["Cod_autorizacao"];    
        }

        $result->close();

        return $authorizationCode;
    }

    function userCodeForCard($card, $conn = null) {
        if($conn == null) 
            $conn = connect();

        $selectSql = "select matricula from Usuario where Rfid = '$card' and Status_usuario = 1";

        $result = mysqli_query($conn, $selectSql);
        
        $userCode = null;

        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userCode = $user["matricula"];    
        }

        $result->close();

        return $userCode;
    }

    function passwordAccessCheck($password, $labNumber) {

        $conn = connect();
        
        $accessGranted = false;

        $authorization = authorizationCodeForPassword($password, $labNumber, $conn);
        
        if($authorization != null) {
            $accessGranted = true;
            $couldRegister = registerAccess($authorization, $labNumber, $conn);

            $accessGranted = $accessGranted && $couldRegister;
        }

        $conn->close();

        return $accessGranted;
    }

    function cardAccessCheck($card, $labNumber) {

        $conn = connect();
        
        $accessGranted = false;

        $user = userCodeForCard($card, $conn);
        
        if($user != null) {
            $accessGranted = true;
            $couldRegister = registerUserAccess($user, $labNumber, $conn);

            $accessGranted = $accessGranted && $couldRegister;
        }

        $conn->close();

        return $accessGranted;

    }
?>
