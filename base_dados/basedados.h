<?php
    define("USER_BD", "root");
    define("PASS_BD", "");
    define("NOME_BD", "roda_expresso");
    $hostname_conn = "localhost";

    // Conectar ao MySQL
    $conn = mysqli_connect($hostname_conn, USER_BD, PASS_BD, NOME_BD);

    // Verificar conexão
    if (!$conn) {
        die("Erro ao conectar ao MySQL: " . mysqli_connect_error());
    }
?>