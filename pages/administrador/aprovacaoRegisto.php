<?php
    session_start();
    include '../../base_dados/basedados.h';

    include '../ConstUtilizadores.php';

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
    }

    // Consultar utilizadores com tipo de utilizador 'UTILIZADOR_POR_VALIDAR'
    $sql = "SELECT id_utilizador, nome_utilizador, email FROM Utilizador WHERE tipo_utilizador = ".UTILIZADOR_POR_VALIDAR;
    $resultado = mysqli_query($conn, $sql);

    if (!$resultado) {
        die("Erro ao consultar utilizadores: " . mysqli_error($conn));
    }

    // Fechar a conexão após a consulta
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovação de Registo | Roda Expresso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            padding-left: 50px;
            font-weight: bold;
            font-style: italic;
            font-size: 36px;
        }
        .menu {
            display: flex;
            gap: 50px;
            padding-right: 150px;
            font-size: 25px;
        }
        .menu a {
            text-decoration: none;
            color: white;
        }
        .negrito {
            font-weight: bold;
            font-size: 26px;
        }
        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
        }
        .registro-container {
            background-color: #007bff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .registro-header {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .registro-header img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 3px solid white;
        }
        .info-utilizador {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            color: white;
        }
        .info-utilizador p {
            margin: 0 0 10px 0;
            font-size: 26px;
            font-weight: normal;
        }
        .botoes-acao {
            display: flex;
            gap: 15px;
        }
        .botao-acao {
            padding: 12px 24px;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            white-space: nowrap;
        }
        .botao-acao:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="PgInicialAdministrador.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="aprovacaoRegisto.php" class="negrito">Aprovação de Registo</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <?php
        // Exibir os utilizadores não validados
        while ($row = mysqli_fetch_assoc($resultado)) {
            echo "
                <div class='registro-container'>
                    <div class='registro-header'>
                        <img src='../../images/homem.png'>
                        <div class='info-utilizador'>
                            <p><span class='negrito'>Nome Completo:</span> {$row['nome_utilizador']}</p>
                            <p><span class='negrito'>Email:</span> {$row['email']}</p>
                        </div>
                    </div>
                    <div class='botoes-acao'>
                        <a href='../aprovar.php?id={$row['id_utilizador']}' class='botao-acao'>Aprovar</a>
                        <a href='../recusar.php?id={$row['id_utilizador']}' class='botao-acao'>Recusar</a>
                    </div>
                </div>
            ";
        }
        ?>
    </div>
</body>
</html> 